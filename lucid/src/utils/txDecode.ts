import {Assets, C, UTxO} from "lucid-cardano";
import {
    Value,
    BigNum,
    Transaction,
    TransactionOutputs
} from "lucid-cardano/types/src/core/wasm_modules/cardano_multiplatform_lib_nodejs/cardano_multiplatform_lib";
import bf from './blockfrost'

const setLovelaceToAssets: (assets: Assets, val: Value) => Assets = (assets: Assets, val: Value) => {
    if (assets['lovelace']) {
        assets['lovelace'] = BigInt(val.coin().to_str()) + BigInt(assets['lovelace'].toString())
    } else {
        assets['lovelace'] = BigInt(val.coin().to_str())
    }
    return assets
}

const setUnitToAssets: (assets: Assets, unit: string, val: BigNum) => Assets = (assets: Assets, unit: string, val: BigNum) => {
    if (assets[unit]) {
        assets[unit] = BigInt(val.to_str()) + BigInt(assets[unit].toString())
    } else {
        assets[unit] = BigInt(val.to_str())
    }
    return assets
}

const decodeTx = async (tx: Transaction, serverWalletAddr: string) => {
    const transaction_body = tx.body()

    const outputs = transaction_body.outputs()
    const inputs = transaction_body.inputs()

    const txInputs: UTxO[] = await getFormattedTxInputs(inputs)
    const serverAdrrInputHashes = txInputs.filter(utxo => utxo && utxo.address === serverWalletAddr).map(utxo => `${utxo.txHash}_${utxo.outputIndex}` )

    const inFromUs = txInputs.filter(input => input.address === serverWalletAddr)
    let inValFromUs: Assets = {}
    inFromUs.forEach(utx => {
        if (utx) Object.keys(utx.assets).forEach(unt =>
            inValFromUs = setUnitToAssets(inValFromUs, unt, BigNum.from_str(utx.assets[unt].toString()))
        )
    })

    const inNotFromUs = txInputs.filter(input => input.address !== serverWalletAddr)
    let inValNotFromUs: Assets = {}
    inNotFromUs.forEach(utx => {
        if (utx) Object.keys(utx.assets).forEach(unt =>
            inValNotFromUs = setUnitToAssets(inValNotFromUs, unt, BigNum.from_str(utx.assets[unt].toString()))
        )
    })

    const outVal = getTxOutValues(outputs, serverWalletAddr)

    const outValToUs = outVal.outValueToUs
    const outValNotToUs = outVal.outValueNotToUs

    return { serverAdrrInputValue: inValFromUs, otherInputValue: inValNotFromUs, outputValueToServerAddr: outValToUs, outputValueToOtherAddr: outValNotToUs, serverAdrrInputHashes: serverAdrrInputHashes }
}

function getTxOutValues(outputs: TransactionOutputs, serverWalletAddr: string) {
    let outValueToUs: Assets = {};
    let outValueNotToUs: Assets = {};

    for (let outputIndex of [...Array(outputs.len()).keys()]) {
        const output = outputs.get(outputIndex)
        const outputAddress = output.address().to_bech32(undefined).toString()
        const outputToServerAddr = outputAddress === serverWalletAddr
        const outputVal: Value = output.amount()
        const outputValJSON = outputVal.to_js_value()

        if (outputToServerAddr) {
            outValueToUs = setLovelaceToAssets(outValueToUs, outputVal);
        } else {
            outValueNotToUs = setLovelaceToAssets(outValueNotToUs, outputVal);
        }
        if(outputValJSON.multiasset) {
            Object.keys(outputValJSON.multiasset).forEach(policyid => {
                const unitVal = outputValJSON.multiasset[policyid]
                Object.keys(unitVal).forEach(nit => {
                    const unitAmnt = unitVal[nit]
                    if (outputToServerAddr) {
                        outValueToUs = setUnitToAssets(outValueToUs, policyid+nit, BigNum.from_str(unitAmnt));
                    } else {
                        outValueNotToUs = setUnitToAssets(outValueNotToUs, policyid+nit, BigNum.from_str(unitAmnt));
                    }
                })
            })
        }
    }
    return { outValueToUs, outValueNotToUs }
}

const getFormattedTxInputs = async (inputs: any) => {
    const txIn: UTxO[] = []
    for (let inputIndex of [...Array(inputs.len()).keys()]) {
        const input = inputs.get(inputIndex);
        const txIndex = Number(input.index().to_str());

        const txHash = Buffer.from(
            input.transaction_id().to_bytes()
        ).toString('hex');

        const tx = await bf({
            endpoint: `/txs/${txHash}/utxos`,
            method: 'GET'
        });

        try {
            const txInput = tx.outputs.filter(
                (row) => row.output_index == txIndex
            )[0]

            if (txInput) {
                const inputUtxo: UTxO = {
                    txHash: txHash,
                    outputIndex: txInput.output_index,
                    assets: (() => {
                        const a: Assets = {};
                        txInput.amount.forEach((am: any) => {
                            a[am.unit] = BigInt(am.quantity);
                        });
                        return a;
                    })(),
                    address: txInput.address,
                    datumHash: txInput.data_hash
                }
                if (inputUtxo.address) txIn.push(inputUtxo)

            }
        } catch (exc) {
            throw 'We could not map the correct input when decoding the transaction.';
        }
    }
    return txIn
}

export { decodeTx }