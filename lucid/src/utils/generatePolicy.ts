import { Blockfrost, Lucid, MintingPolicy } from "lucid-cardano";

const generatePolicy = async (lucid: Lucid) => {
    const { paymentCredential } = lucid.utils.getAddressDetails(
        await lucid.wallet.address(),
    );
    const mintingPolicy: MintingPolicy = lucid.utils.nativeScriptFromJson({
        type: 'all',
        scripts: [
            {
                type: 'sig',
                keyHash: paymentCredential?.hash!,
            },
        ],
    });

    return mintingPolicy;
}

export default generatePolicy;