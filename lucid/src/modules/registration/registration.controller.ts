import { Controller, Post, Req} from '@nestjs/common';
import {PolicyId, Tx, TxComplete, fromText} from 'lucid-cardano';
import { Request } from 'express';
import getConfigs from '../../utils/getConfigs.js';
import generatePolicy from '../../utils/generatePolicy.js';
import _ from 'lodash';

@Controller('registration')
export class RegistrationController {
    @Post('mint')
    public async mintNft(@Req() request: Request) {
        const [voter] = await getConfigs(request);
        const [minter] = await getConfigs(request);

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
            [unit]: 1n,
          })
          .mintAssets({ [unit]: 1n })
          .validTo(Date.now() + 250000)
          .attachMintingPolicy(mintingPolicy)
          .attachMetadata(721, metadata).complete();

        return tx.toString();
    }

    @Post('submit')
    public async submitRegistration(@Req() request: Request) {        
        const [lucid] = await getConfigs(request);
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

