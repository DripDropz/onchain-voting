import express from 'express';
import {generatePolicy, getLucid} from "../lib/util.mjs";
import {toUnit} from "lucid-cardano";

const router = express.Router();

router.post('/mint', async (req, res) => {
    if (!req.body?.votingSeed || !req.body?.registrationSeed) {
        res.status(400).send("Invalid Wallet Data.");
        return;
    }

    const votingMinter = await getLucid(req);
    votingMinter.selectWalletFromSeed(req.body?.votingSeed);

    const registrationMinter = await getLucid(req);
    registrationMinter.selectWalletFromSeed(req.body.registrationSeed);

    const registration = req.body?.registration;
    const assets = Object.keys(registration?.registration?.assets);
    const registrationToken = assets.find((asset) => asset.includes(registration.policyId));

    const registrationPolicy = await generatePolicy(registrationMinter);

    const assetName = req.body?.assetName;

    const votingPolicy = await generatePolicy(votingMinter);
    const votingPolicyId = votingMinter.utils.mintingPolicyToId(votingPolicy);
    const votingUnit = toUnit(votingPolicyId, assetName);

    const utxos = req.body?.utxos;

    const voter = await getLucid(req);

    voter.selectWalletFrom({
        address: registration?.registration?.address,
        utxos: [registration.registration, ...utxos]
    });

    const voterId = req.body?.voterId;

    const votingMetadata = {
        voter_id: voterId,
        voter_power: req.body?.votingPower,
        ballot_id: req.body?.ballotHash,
        choices: req.body?.choices,
    };

    const votingMinterAddress = await votingMinter.wallet.address()
    const registrationMinterAddress = await registrationMinter.wallet.address()

    try {
        const tx = await voter
          .newTx()
          .readFrom(utxos)
          .payToAddress(votingMinterAddress, {
              [votingUnit]: 1n,
          })
          // Voting
          .attachMintingPolicy(votingPolicy)
          .mintAssets({[votingUnit]: 1n})
          .addSigner(votingMinterAddress)
          // Registration
          .attachMintingPolicy(registrationPolicy)
          .mintAssets({[registrationToken]: -1n})
          .addSigner(registrationMinterAddress)

          .validTo(Date.now() + 250000)
          .attachMetadata(446, [votingMetadata])
          .complete()

        console.log({tx: tx.toString()});

        res.send({tx: tx.toString()});
    } catch (e) {
        console.error('Vote/Mint Error', e);
        res.status(400).send("Vote/Mint transaction failed");
    }
});

router.post('/submit', async (req, res) => {
    const votingMinter = await getLucid(req);
    votingMinter.selectWalletFromSeed(req.body?.votingSeed);

    const registrationMinter = await getLucid(req);
    registrationMinter.selectWalletFromSeed(req.body.registrationSeed);

    const registrationTx = registrationMinter.fromTx(req.body?.tx);
    const registrationWitness = await registrationTx.partialSign();

    const voteTx = votingMinter.fromTx(req.body?.tx);
    const multiSignedTx = await (
        voteTx.assemble([
            req.body.witnesses,
            registrationWitness
        ]).sign()
    ).complete();

    const txHash = await multiSignedTx.submit();

    const processed = await votingMinter.awaitTx(txHash);
    const changeUtxo = await votingMinter.utxosByOutRef([{txHash, outputIndex: 0}]);

    const refundTxComplete = await votingMinter
        .newTx()
        .payToAddress(req.body.voterAddress, {
            lovelace: changeUtxo[0].assets.lovelace
        })
        .complete();
    const refundTxSigned = await refundTxComplete.sign().complete();
    const refundTxHash = await refundTxSigned.submit();

    res.send(txHash);
})

export default router;