import { Controller, HttpException, HttpStatus, Post, Req } from '@nestjs/common';
import { Blockfrost, fromHex, fromUnit, Lucid, M, toHex, toText } from "lucid-cardano";
import { Request } from "express";

@Controller('wallet')
export class WalletController {
    @Post('address')
    async wallet(@Req() request: Request) {
        let lucid;
        [lucid] = await this.getConfigs(request);
        lucid.selectWalletFromSeed(request?.body?.seed);

        return {
            address: await lucid.wallet.address()
        };
    }

    @Post('balances')
    async balances(@Req() request: Request) {
        let lucid;
        [lucid] = await this.getConfigs(request);
        lucid.selectWalletFromSeed(request?.body?.seed);

        let utxos = await lucid.wallet.getUtxos();
        utxos = utxos.map(utxo => utxo.assets);

        const balances = {};
        utxos.forEach((asset) =>
            Object.keys(asset).forEach(key => {
                balances[key] = asset[key] + (balances[key] || BigInt(0));
            })
        );

        Object.keys(balances).forEach(key => {
            let props = {};
            if (key == 'lovelace') {
                props = {
                    name: key
                };
            } else {
                props = fromUnit(key);
                props['name'] = toText(props['name']);
            }
            balances[key] = {
                asset: key,
                amount: balances[key],
                ...props
            };
        })

        return this.toObject(balances);
    }

    @Post('authenticate')
    async authenticate(@Req() request: Request) {
        const signedMessage = request?.body;
        const cose1 = M.COSESign1.from_bytes(fromHex(signedMessage.signature));
        const protectedHeaders = cose1.headers().protected()
            .deserialized_headers();
        const cose1Address = (() => {
            try {
                return toHex(protectedHeaders.header(M.Label.new_text("address"))?.as_bytes());
            }
            catch (_e) {
                throw new Error("No address found in signature.");
            }
        })();
        return { 'addrHex': cose1Address };

    }


    protected async getConfigs(request: Request) {
        if (!request?.body?.seed) {
            throw new HttpException('Invalid Data', HttpStatus.NOT_ACCEPTABLE);
        }
        const projectId = process.env.BLOCKFROST_PROJECT_ID || 'preview2QfIR5epKjaFmh54Id75yXAM7yStk3vc';
        const blockfrostUrl = projectId.includes('preview') ? "https://cardano-preview.blockfrost.io/api/v0" : "https://cardano-mainnet.blockfrost.io/api/v0";
        const cardanoNetwork = projectId.includes('preview') ? "Preview" : "Mainnet";

        const lucid = await Lucid.new(
            new Blockfrost(blockfrostUrl, projectId),
            cardanoNetwork,
        );

        return [lucid, projectId, blockfrostUrl, cardanoNetwork];
    }

    protected toObject(obj) {
        return JSON.parse(JSON.stringify(obj, (key, value) =>
            typeof value === 'bigint'
                ? value.toString()
                : value // return everything else unchanged
        ));
    }
}