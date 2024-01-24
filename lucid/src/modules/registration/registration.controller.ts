import {Controller, Inject, Post, Req} from '@nestjs/common';
import {PolicyId, Tx, TxComplete, fromText} from 'lucid-cardano';
import {Request} from 'express';
import generatePolicy from '../../utils/generatePolicy.js';
import _ from 'lodash';
import {AppConfigService} from '../../services/app-config.service.js';

@Controller('registration')
export class RegistrationController {
    public constructor(
        @Inject(AppConfigService) private readonly configService: AppConfigService,
    ) {
    }

    @Post('mint')
    public async mintNft(@Req() request: Request) {
        const [voter] = await this.configService.getConfigs(request);
        const [minter] = await this.configService.getConfigs(request);

        minter.selectWalletFromSeed(request?.body?.seed);
        const mintingPolicy = await generatePolicy(minter);
        const policyId: PolicyId = minter.utils.mintingPolicyToId(mintingPolicy);
        const assetName = fromText(request?.body?.assetName);
        const unit = policyId + assetName;
        const metadata = {
            [policyId]: {
                [assetName]: {
                    ...request?.body?.metadata
                }
            }
        };

        const utxos = await voter.utxosAt(request?.body?.voterAddress);
        voter.selectWalletFrom({
            address: request?.body?.voterAddress,
            utxos
        });

        const tx: TxComplete = await voter
            .newTx()
            .readFrom(utxos)
            .payToAddress(request?.body?.voterAddress, {
                lovelace: 2000000n,
                [unit]: 1n,
            })
            .mintAssets({[unit]: 1n})
            .validTo(Date.now() + 250000)
            .attachMintingPolicy(mintingPolicy)
            .attachMetadata(721, metadata).complete();

        return tx.toString();
    }

    @Post('submit')
    public async submitRegistration(@Req() request: Request) {
        const [lucid] = await this.configService.getConfigs(request);
        lucid.selectWalletFromSeed(request?.body?.seed);

        // deconstruct and validate tx.
        const tx = lucid.fromTx(request.body?.tx);

        // complete signing tx.
        const multiSignedTx = await (tx.assemble([request.body.witnesses]).sign()).complete();

        //submit tx to the network
        const txId = await multiSignedTx.submit();
        return txId;
    }
}

