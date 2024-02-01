import {getLucid} from './util.mjs'

const checkHeader = async (req, res, next) => {
    try {
        const cardano_network = req.headers['cardano-network'];
        switch (cardano_network) {
            case 'preview':
                req.network = 'Preview';
                req.project_id = process.env.BF_PREVIEW_ID;
                break;
            case 'preprod':
                req.network = 'Preprod';
                req.project_id = process.env.BF_PREPROD_ID;
                break;
            case 'mainnet':
                req.network = 'Mainnet';
                req.project_id = process.env.BF_MAINNET_ID;
                break;
            default:
                throw new Error(`Invalid network!`);
        }
    } catch (e) {
        res.status(400).send(e.message);
        return
    }

    try {
        req.Lucid = await getLucid(req);

        if (req.body?.seed) {
            await req.Lucid.selectWalletFromSeed(req.body?.seed);
        }
        next()
    } catch (e) {
        console.error(e);
        res.status(400).send("Could not connect to Blockfrost!");
    }
}

export {
    checkHeader,
    getLucid
}
