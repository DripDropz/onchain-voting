import {Blockfrost, Lucid} from "lucid-cardano";

const generatePolicy = async (lucid) => {
    const {paymentCredential} = lucid.utils.getAddressDetails(
        await lucid.wallet.address(),
    );
    return lucid.utils.nativeScriptFromJson({
        type: 'all',
        scripts: [
            {
                type: 'sig',
                keyHash: paymentCredential?.hash
            }
        ]
    });
}

const getLucid = async (request) => {
    if (!request.project_id) {
        throw new Error(`Invalid Project ID`);
    }


    let blockfrostUrl = "";
    switch (request.network) {
        case 'Preview':
            blockfrostUrl = "https://cardano-preview.blockfrost.io/api/v0";
            break;
        case 'Preprod':
            blockfrostUrl = "https://cardano-preprod.blockfrost.io/api/v0";
            break;
        case 'Mainnet':
            blockfrostUrl = "https://cardano-mainnet.blockfrost.io/api/v0";
            break;
        default:
            throw new Error(`Invalid network!`);
    }

    return await Lucid.new(
        new Blockfrost(blockfrostUrl, request.project_id),
        request.network
    );

}

const toObject = (obj) => {
    return JSON.parse(
        JSON.stringify(
            obj,
            (key, value) => (typeof value === 'bigint' ? value.toString() : value)
        )
    )
}

export {
    generatePolicy,
    getLucid,
    toObject
};