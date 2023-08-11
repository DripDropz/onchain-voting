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
exports.__esModule = true;
var blockfrostRequest = function (_a) {
    var _b = _a.body, body = _b === void 0 ? null : _b, _c = _a.endpoint, endpoint = _c === void 0 ? '' : _c, _d = _a.headers, headers = _d === void 0 ? {} : _d, _e = _a.method, method = _e === void 0 ? 'GET' : _e;
    return __awaiter(void 0, void 0, void 0, function () {
        var networkEndpoint, blockfrostApiKey;
        return __generator(this, function (_f) {
            networkEndpoint = process.env.NETWORK === '0' ? 'https://cardano-preview.blockfrost.io/api/v0' : 'https://cardano-mainnet.blockfrost.io/api/v0' //process.env.BLOCKFROST_URL ? process.env.BLOCKFROST_URL : ''
            ;
            blockfrostApiKey = process.env.NETWORK === '0' ? 'preview2QfIR5epKjaFmh54Id75yXAM7yStk3vc' : 'preview2QfIR5epKjaFmh54Id75yXAM7yStk3vc';
            try {
                // return await (
                //     await fetch(`${networkEndpoint}${endpoint}`, {
                //         headers: {
                //             project_id: blockfrostApiKey,
                //             ...headers,
                //         },
                //         method,
                //         body,
                //     })
                // ).json();
            }
            catch (error) {
                return [2 /*return*/, error];
            }
            return [2 /*return*/];
        });
    });
};
exports["default"] = blockfrostRequest;
