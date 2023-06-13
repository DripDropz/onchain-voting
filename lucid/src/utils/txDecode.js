"use strict";
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
var __generator = (this && this.__generator) || function (thisArg, body) {
    var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g;
    return g = { next: verb(0), "throw": verb(1), "return": verb(2) }, typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
    function verb(n) { return function (v) { return step([n, v]); }; }
    function step(op) {
        if (f) throw new TypeError("Generator is already executing.");
        while (_) try {
            if (f = 1, y && (t = op[0] & 2 ? y["return"] : op[0] ? y["throw"] || ((t = y["return"]) && t.call(y), 0) : y.next) && !(t = t.call(y, op[1])).done) return t;
            if (y = 0, t) op = [op[0] & 2, t.value];
            switch (op[0]) {
                case 0: case 1: t = op; break;
                case 4: _.label++; return { value: op[1], done: false };
                case 5: _.label++; y = op[1]; op = [0]; continue;
                case 7: op = _.ops.pop(); _.trys.pop(); continue;
                default:
                    if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) { _ = 0; continue; }
                    if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) { _.label = op[1]; break; }
                    if (op[0] === 6 && _.label < t[1]) { _.label = t[1]; t = op; break; }
                    if (t && _.label < t[2]) { _.label = t[2]; _.ops.push(op); break; }
                    if (t[2]) _.ops.pop();
                    _.trys.pop(); continue;
            }
            op = body.call(thisArg, _);
        } catch (e) { op = [6, e]; y = 0; } finally { f = t = 0; }
        if (op[0] & 5) throw op[1]; return { value: op[0] ? op[1] : void 0, done: true };
    }
};
var __spreadArray = (this && this.__spreadArray) || function (to, from, pack) {
    if (pack || arguments.length === 2) for (var i = 0, l = from.length, ar; i < l; i++) {
        if (ar || !(i in from)) {
            if (!ar) ar = Array.prototype.slice.call(from, 0, i);
            ar[i] = from[i];
        }
    }
    return to.concat(ar || Array.prototype.slice.call(from));
};
exports.__esModule = true;
exports.decodeTx = void 0;
var cardano_multiplatform_lib_1 = require("lucid-cardano/types/src/core/wasm_modules/cardano_multiplatform_lib_nodejs/cardano_multiplatform_lib");
var blockfrost_1 = require("./blockfrost");
var setLovelaceToAssets = function (assets, val) {
    if (assets['lovelace']) {
        assets['lovelace'] = BigInt(val.coin().to_str()) + BigInt(assets['lovelace'].toString());
    }
    else {
        assets['lovelace'] = BigInt(val.coin().to_str());
    }
    return assets;
};
var setUnitToAssets = function (assets, unit, val) {
    if (assets[unit]) {
        assets[unit] = BigInt(val.to_str()) + BigInt(assets[unit].toString());
    }
    else {
        assets[unit] = BigInt(val.to_str());
    }
    return assets;
};
var decodeTx = function (tx, serverWalletAddr) { return __awaiter(void 0, void 0, void 0, function () {
    var transaction_body, outputs, inputs, txInputs, serverAdrrInputHashes, inFromUs, inValFromUs, inNotFromUs, inValNotFromUs, outVal, outValToUs, outValNotToUs;
    return __generator(this, function (_a) {
        switch (_a.label) {
            case 0:
                transaction_body = tx.body();
                outputs = transaction_body.outputs();
                inputs = transaction_body.inputs();
                return [4 /*yield*/, getFormattedTxInputs(inputs)];
            case 1:
                txInputs = _a.sent();
                serverAdrrInputHashes = txInputs.filter(function (utxo) { return utxo && utxo.address === serverWalletAddr; }).map(function (utxo) { return "".concat(utxo.txHash, "_").concat(utxo.outputIndex); });
                inFromUs = txInputs.filter(function (input) { return input.address === serverWalletAddr; });
                inValFromUs = {};
                inFromUs.forEach(function (utx) {
                    if (utx)
                        Object.keys(utx.assets).forEach(function (unt) {
                            return inValFromUs = setUnitToAssets(inValFromUs, unt, cardano_multiplatform_lib_1.BigNum.from_str(utx.assets[unt].toString()));
                        });
                });
                inNotFromUs = txInputs.filter(function (input) { return input.address !== serverWalletAddr; });
                inValNotFromUs = {};
                inNotFromUs.forEach(function (utx) {
                    if (utx)
                        Object.keys(utx.assets).forEach(function (unt) {
                            return inValNotFromUs = setUnitToAssets(inValNotFromUs, unt, cardano_multiplatform_lib_1.BigNum.from_str(utx.assets[unt].toString()));
                        });
                });
                outVal = getTxOutValues(outputs, serverWalletAddr);
                outValToUs = outVal.outValueToUs;
                outValNotToUs = outVal.outValueNotToUs;
                return [2 /*return*/, { serverAdrrInputValue: inValFromUs, otherInputValue: inValNotFromUs, outputValueToServerAddr: outValToUs, outputValueToOtherAddr: outValNotToUs, serverAdrrInputHashes: serverAdrrInputHashes }];
        }
    });
}); };
exports.decodeTx = decodeTx;
function getTxOutValues(outputs, serverWalletAddr) {
    var outValueToUs = {};
    var outValueNotToUs = {};
    var _loop_1 = function (outputIndex) {
        var output = outputs.get(outputIndex);
        var outputAddress = output.address().to_bech32(undefined).toString();
        var outputToServerAddr = outputAddress === serverWalletAddr;
        var outputVal = output.amount();
        var outputValJSON = outputVal.to_js_value();
        if (outputToServerAddr) {
            outValueToUs = setLovelaceToAssets(outValueToUs, outputVal);
        }
        else {
            outValueNotToUs = setLovelaceToAssets(outValueNotToUs, outputVal);
        }
        if (outputValJSON.multiasset) {
            Object.keys(outputValJSON.multiasset).forEach(function (policyid) {
                var unitVal = outputValJSON.multiasset[policyid];
                Object.keys(unitVal).forEach(function (nit) {
                    var unitAmnt = unitVal[nit];
                    if (outputToServerAddr) {
                        outValueToUs = setUnitToAssets(outValueToUs, policyid + nit, cardano_multiplatform_lib_1.BigNum.from_str(unitAmnt));
                    }
                    else {
                        outValueNotToUs = setUnitToAssets(outValueNotToUs, policyid + nit, cardano_multiplatform_lib_1.BigNum.from_str(unitAmnt));
                    }
                });
            });
        }
    };
    for (var _i = 0, _a = __spreadArray([], Array(outputs.len()).keys(), true); _i < _a.length; _i++) {
        var outputIndex = _a[_i];
        _loop_1(outputIndex);
    }
    return { outValueToUs: outValueToUs, outValueNotToUs: outValueNotToUs };
}
var getFormattedTxInputs = function (inputs) { return __awaiter(void 0, void 0, void 0, function () {
    var txIn, _loop_2, _i, _a, inputIndex;
    return __generator(this, function (_b) {
        switch (_b.label) {
            case 0:
                txIn = [];
                _loop_2 = function (inputIndex) {
                    var input, txIndex, txHash, tx, txInput_1, inputUtxo;
                    return __generator(this, function (_c) {
                        switch (_c.label) {
                            case 0:
                                input = inputs.get(inputIndex);
                                txIndex = Number(input.index().to_str());
                                txHash = Buffer.from(input.transaction_id().to_bytes()).toString('hex');
                                return [4 /*yield*/, (0, blockfrost_1["default"])({
                                        endpoint: "/txs/".concat(txHash, "/utxos"),
                                        method: 'GET'
                                    })];
                            case 1:
                                tx = _c.sent();
                                try {
                                    txInput_1 = tx.outputs.filter(function (row) { return row.output_index == txIndex; })[0];
                                    if (txInput_1) {
                                        inputUtxo = {
                                            txHash: txHash,
                                            outputIndex: txInput_1.output_index,
                                            assets: (function () {
                                                var a = {};
                                                txInput_1.amount.forEach(function (am) {
                                                    a[am.unit] = BigInt(am.quantity);
                                                });
                                                return a;
                                            })(),
                                            address: txInput_1.address,
                                            datumHash: txInput_1.data_hash
                                        };
                                        if (inputUtxo.address)
                                            txIn.push(inputUtxo);
                                    }
                                }
                                catch (exc) {
                                    throw 'We could not map the correct input when decoding the transaction.';
                                }
                                return [2 /*return*/];
                        }
                    });
                };
                _i = 0, _a = __spreadArray([], Array(inputs.len()).keys(), true);
                _b.label = 1;
            case 1:
                if (!(_i < _a.length)) return [3 /*break*/, 4];
                inputIndex = _a[_i];
                return [5 /*yield**/, _loop_2(inputIndex)];
            case 2:
                _b.sent();
                _b.label = 3;
            case 3:
                _i++;
                return [3 /*break*/, 1];
            case 4: return [2 /*return*/, txIn];
        }
    });
}); };
