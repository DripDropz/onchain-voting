import express from 'express';
import {generatePolicy} from '../lib/util.js'
import {fromText} from "lucid-cardano";

const router = express.Router();

router.post('/mint', async (req, res) => {
    if (!req.Lucid) {
        res.status(400).send("No Blockfrost connection! Did you set your Project ID right?")
    }

/*    try {
        await req.Lucid.selectWalletFromSeed(req.body?.seed);
    } catch (e) {
        res.status(400).send("Invalid seed!");
        return;
    }*/

    if (!req.Lucid.wallet) {
        res.status(400).send("Invalid seed!");
        return;
    }

    const mintingPolicy = await generatePolicy(req.Lucid);
    const policyId = req.Lucid.utils.mintingPolicyToId(mintingPolicy);
    const assetName = fromText(req.body?.assetName);
    const unit = policyId + assetName;
    const metadata = {
        [policyId]: {
            [assetName]: {
                ...req.body?.metadata
            }
        }
    }

    const utxos = await req.Lucid.utxosAt(req.body?.voterAddress);
    req.Lucid.selectWalletFrom({
        address: req.body?.voterAddress,
        utxos
    });

    const tx = await req.Lucid
        .newTx()
        .readFrom(utxos)
        .payToAddress(req.body?.voterAddress, {
            [unit]: 1n
        })
        .mintAssets({[unit]: 1n})
        .validTo(Date.now() + 250000)
        .attachMintingPolicy(mintingPolicy)
        .attachMetadata(721, metadata)
        .complete()

    res.send(tx.toString())
})

router.post('/submit', async (req, res) => {
    const tx = req.Lucid.fromTx(req.body?.tx);
    const multiSignedTx = await (tx.assemble([req.body?.witnesses]).sign()).complete();
    const txId = await multiSignedTx.submit();
    return txId;
})

export default router;