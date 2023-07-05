import {
    Controller,
    HttpException,
    HttpStatus,
    Post,
    Req,
} from '@nestjs/common';
import {
    Blockfrost,
    Lucid,
    MintingPolicy,
    PolicyId,
    Unit,
    fromText,
    toHex,
    toText,
} from 'lucid-cardano';
import { Request } from 'express';

@Controller('vote')
export class VoteController {
    @Post('mint')
    public async mintNft(@Req() request: Request) {
        let lucid;
        [lucid] = await this.getConfigs(request);

        lucid.selectWalletFromSeed(request?.body?.seed);
        lucid.selectWalletFrom;
        const mintingPolicy = await this.getPolicy(lucid);
        const policyId: PolicyId = lucid.utils.mintingPolicyToId(mintingPolicy);
        const unit: Unit = policyId;
        const metadata = {
          voter_id: request?.body?.voterId,
          voter_power: request?.body?.votingPower,
          ballot_id: request?.body?.ballotId,
          choices: [[request?.body?.choices]],
        };

        const tx = await lucid
          .newTx()
          .payToAddress(request?.body?.voterAddress, {
            lovelace: BigInt(2000000),
            [unit]: 1n,
          })
          .mintAssets({ [unit]: 1n })
          .validTo(Date.now() + 100000)
          .attachMintingPolicy(mintingPolicy)
          .attachMetadata(446, [metadata])
          .complete();

        const signedTx = await tx.sign().complete();
        const hash = await signedTx.submit();

        return hash ;
    }

    protected async getPolicy(lucid: Lucid) {
        const { paymentCredential } = lucid.utils.getAddressDetails(
            await lucid.wallet.address(),
        );
        const mintingPolicy: MintingPolicy = lucid.utils.nativeScriptFromJson({
            type: 'all',
            scripts: [
                {
                    type: 'before',
                    slot: lucid.utils.unixTimeToSlot(1761942369100),
                },
                {
                    type: 'sig',
                    keyHash: paymentCredential?.hash!,
                },
            ],
        });

        return mintingPolicy;
    }

    protected async getConfigs(request: Request) {
        if (!request?.body?.seed) {
            throw new HttpException('Invalid Data', HttpStatus.NOT_ACCEPTABLE);
        }
        const projectId =
            process.env.BLOCKFROST_PROJECT_ID ||
            'preview2QfIR5epKjaFmh54Id75yXAM7yStk3vc';
        const blockfrostUrl = projectId.includes('preview')
            ? 'https://cardano-preview.blockfrost.io/api/v0'
            : 'https://cardano-mainnet.blockfrost.io/api/v0';
        const cardanoNetwork = projectId.includes('preview')
            ? 'Preview'
            : 'Mainnet';

        const lucid = await Lucid.new(
            new Blockfrost(blockfrostUrl, projectId),
            cardanoNetwork,
        );

        return [lucid, projectId, blockfrostUrl, cardanoNetwork];
    }
}
