import {Controller, Get, Inject, Post, Req } from '@nestjs/common';
import {fromHex, fromUnit, getAddressDetails, Lucid, M, MintingPolicy, toHex, toText,} from 'lucid-cardano';
import {Request} from 'express';
import {AppConfigService} from '../../services/app-config.service.js';
import https from 'https';

@Controller('wallet')
export class WalletController {
    public constructor(
        @Inject(AppConfigService) private readonly configService: AppConfigService,
    ) {}

    @Post('get-policy-id')
    public async mintPolicyId(@Req() request: Request) {
        const [lucid] = await this.configService.getConfigs(request);
        return lucid.utils.mintingPolicyToId(
            await this.mintPolicy(request)
        );
    }

    @Post('get-policy')
    public async mintPolicy(@Req() request: Request) {
        const [lucid] = await this.configService.getConfigs(request);
        lucid.selectWalletFromSeed(request?.body?.seed);
        return (await this.getPolicy(lucid));
    }
  
    protected async getPolicy(lucid: Lucid) {
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

    @Post('address')
    async wallet(@Req() request: Request) {
        const [lucid] = await this.configService.getConfigs(request);
        lucid.selectWalletFromSeed(request?.body?.seed);

        return {
            address: await lucid.wallet.address(),
        };
    }

    @Post('balances')
    async balances(@Req() request: Request) {
        const [lucid] = await this.configService.getConfigs(request);
        lucid.selectWalletFromSeed(request?.body?.seed);

        let utxos = (await lucid.wallet.getUtxos())
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

        return this.toObject(balances);
    }

    @Post('balance')
    async balance(@Req() request: Request) {
        try {
            const [lucid, appUrl] = await this.configService.getConfigs(request);
            lucid.selectWalletFromSeed(request?.body?.seed);
            const stakeAddr = await lucid.wallet.rewardAddress();
    
            const url = `${appUrl}/api/query/accounts/${stakeAddr}`;
            const response = await new Promise((resolve, reject) => {
                https.get(url, (res) => {
                    let data = '';
                    res.on('data', (chunk) => {
                        data += chunk;
                    });
    
                    res.on('end', () => {
                        try {
                            const stakeResponse = JSON.parse(data);
                            resolve(stakeResponse.data.controlled_amount / 1000000);
                        } catch (error) {
                            console.error('Error parsing response data:', error.message);
                            reject(error);
                        }
                    });
                }).on('error', reject);
            });
    
            return response;
        } catch (error) {
            console.error('Error fetching wallet balance:', error.message);
            throw error; 
        }
    }

    @Post('authenticate')
    async authenticate(@Req() request: Request) {
        if (request?.body?.signature) {
            const signedMessage = request?.body?.signature;
            const cose1 = M.COSESign1.from_bytes(fromHex(signedMessage));
            const protectedHeaders = cose1
                .headers()
                .protected()
                .deserialized_headers();
            const cose1Address = (() => {
                try {
                    return toHex(
                        protectedHeaders.header(M.Label.new_text('address'))?.as_bytes(),
                    );
                } catch (_e) {
                    throw new Error('No address found in signature.');
                }
            })();
            return cose1Address;
        }

        const txHash = request?.body?.txHash
        const stakeCredential = getAddressDetails(request?.body?.stakeAddr)?.stakeCredential;

        return txHash.includes(stakeCredential.hash);
    }

    protected toObject(obj) {
        return JSON.parse(
            JSON.stringify(
                obj,
                (key, value) => (typeof value === 'bigint' ? value.toString() : value), // return everything else unchanged
            ),
        );
    }
}
