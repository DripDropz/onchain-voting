import {
    Controller,
    HttpException,
    HttpStatus,
    Inject,
    Post,
    Req,
} from '@nestjs/common';
import {
    PolicyId,
    UTxO,
    Unit,
    toUnit,
} from 'lucid-cardano';
import { Request } from 'express';
import generatePolicy from '../../utils/generatePolicy.js';
import { AppConfigService } from '../../services/app-config.service.js';

@Controller('vote')
export class VoteController {
  public constructor(
    @Inject(AppConfigService) private readonly configService: AppConfigService,
  ) {}

    @Post('mint')
    public async mintNft(@Req() request: Request) {
      if (!request?.body?.votingSeed || !request?.body?.registrationSeed) {
        throw new HttpException('Invalid Wallet Data. ', HttpStatus.NOT_ACCEPTABLE);
      }
      const [voter] = await this.configService.getConfigs(request);

      const registration: {policyId: string, registration: UTxO} = request?.body?.registration;
      const assets = Object.keys(registration?.registration?.assets);
      const registrationToken = assets.find((asset) => asset.includes(registration.policyId));

      // Ballot Registration wallet
      const [registrationMinter] = await this.configService.getConfigs(request);
      registrationMinter.selectWalletFromSeed(request?.body?.registrationSeed);
      const registrationPolicy = await generatePolicy(registrationMinter);

      // Ballot Voting wallet
      const assetName = request?.body?.assetName;
      const [votingMinter] = await this.configService.getConfigs(request);
      votingMinter.selectWalletFromSeed(request?.body?.votingSeed);
      const votingPolicy = await generatePolicy(votingMinter);
      const votingPolicyId: PolicyId = votingMinter.utils.mintingPolicyToId(votingPolicy);
      const votingUnit: Unit = toUnit(votingPolicyId, assetName);

       // User wallet
       const utxos: UTxO[] = request?.body?.utxos;

       voter.selectWalletFrom({
           address: registration?.registration?.address,
           utxos: [registration.registration, ...utxos]
       });
      const voterId = request?.body?.voterId;
     
      const votingMetadata = {
        voter_id: voterId,
        voter_power: request?.body?.votingPower,
        ballot_id: request?.body?.ballotHash,
        choices: request?.body?.choices,
      };

      const tx = await voter
        .newTx()
        .readFrom(utxos)
        .payToAddress((await votingMinter.wallet.address()), {
          [votingUnit]: 1n,
        })

        // voting 
        .attachMintingPolicy(votingPolicy)
        .mintAssets({ [votingUnit]: 1n })
        .addSigner(await votingMinter.wallet.address())

        // Registration
        .attachMintingPolicy(registrationPolicy)
        .mintAssets({ [registrationToken]: -1n })
        .addSigner(await registrationMinter.wallet.address())
        
        .validTo(Date.now() + 250000)       
        .attachMetadata(446, [votingMetadata])
        .complete();

      return {tx: tx.toString()};
    }

    @Post('submit')
    public async submitRegistration(@Req() request: Request) {
      const [votingMinter] = await this.configService.getConfigs(request);
      votingMinter.selectWalletFromSeed(request?.body?.votingSeed);

      const [registrationMinter] = await this.configService.getConfigs(request);
      registrationMinter.selectWalletFromSeed(request?.body?.registrationSeed);

      // get registration signature for burn tx
      const registrationTx = registrationMinter.fromTx(request.body?.tx);
      const registrationWitness = await registrationTx.partialSign();

       // deconstruct and validate tx.
       const voteTx = votingMinter.fromTx(request.body?.tx);

      // complete signing tx.
      const multiSignedTx = await (
        voteTx.assemble([
          request.body.witnesses,
          registrationWitness
        ]).sign()
      ).complete();

      //submit tx to the network
      const txHash = await multiSignedTx.submit();

      // process refund
      const processed =  await votingMinter.awaitTx(txHash);
      const changeUtxo = await votingMinter.utxosByOutRef([{txHash, outputIndex: 0}]);

      const refundTxComplete = await votingMinter.newTx().payToAddress(request.body.voterAddress, {lovelace: changeUtxo[0].assets.lovelace}).complete();
      const refundTxSigned = await refundTxComplete.sign().complete();
      const refundTxHash = await refundTxSigned.submit();

      return txHash;
    }
}
