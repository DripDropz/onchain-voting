import express from 'express';
import {generatePolicy, toObject} from "../lib/util.mjs";
import {fromHex, fromUnit, getAddressDetails, M, toHex, toText} from "lucid-cardano";

const router = express.Router();

router.post('/get-policy-id', async (req, res) => {
    res.send(await req.Lucid.utils.mintingPolicyToId(await generatePolicy(req.Lucid)));
});

router.post('/get-policy', async (req, res) => {
    res.send(await generatePolicy(req.Lucid));
});

router.post('/address', async (req, res) => {
    const address = await req.Lucid.wallet.address();
    res.send({"address": address});
});

router.post('/balances', async (req, res) => {
    let utxos = (await req.Lucid.wallet.getUtxos())
        .map((utxo) => utxo.assets);

    const balances = {};
    utxos.forEach((asset) =>
        Object.keys(asset).forEach((key) => {
            balances[key] = asset[key] + (balances[key] || 0n)
        })
    )

    Object.keys(balances).forEach((key) => {
        let props = {};
        if (key === "lovelace") {
            props = {
                name: key,
            }
        } else {
            props = fromUnit(key);
            props['name'] = toText(props['name'])
        }
        balances[key] = {
            asset: key,
            amount: balances[key],
            ...props
        };
    });

    res.send(toObject(balances));
})

router.post('/authenticate', async (req, res) => {
    if (req.body?.signature) {
        const signedMessage = req.body?.signature;
        const cose1 = M.COSESign1.from_bytes(fromHex(signedMessage));
        const protectedHeaders = cose1.headers().protected().deserialized_headers();
        const cose1Address = (() => {
            try {
                return toHex(
                    protectedHeaders.header(M.Label.new_text('address'))?.as_bytes()
                );
            } catch (e) {
                throw new Error(`No address found in signature.`);
            }
        })();
        res.send(cose1Address);
        return;
    }

    const txHash = req.body?.txHash;
    const stakeCredential = getAddressDetails(req.body?.stakeAddr)?.stakeCredential;

    res.send(txHash.includes(stakeCredential.hash));
})

export default router;