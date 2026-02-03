import { Blockfrost, Lucid } from 'lucid-cardano';
import { Request } from 'express';
import { Injectable } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';

// @Injectable()
// export class AppConfigService {
//   constructor(private configService: ConfigService) {}

//   async getConfigs(request: Request): Promise<[Lucid, string]> {
//     const networkId = this.configService.get('CARDANO_NETWORK');
//     const appUrl = this.configService.get('APP_URL');
//     console.log(networkId)
//     console.log(appUrl)
//     let cardanoNetwork;
//     switch (networkId) {
//       case '0':
//         cardanoNetwork = 'Preview';
//         break;

//       case '1':
//         cardanoNetwork = 'Mainnet';
//         break;
//       default:
//         throw new Error('Invalid network');
//     }

//     const lucid = await Lucid.new(
//       new Blockfrost(`${appUrl}/api/query`),
//       cardanoNetwork,
//     );

//     return [lucid, cardanoNetwork, appUrl];
//   }
// }

@Injectable()
export class AppConfigService {
    constructor(
        private configService: ConfigService
    ) { }

    async getConfigs(request: Request): Promise<[Lucid, string, string, string, string]> {
        const projectId = this.configService.get('BLOCKFROST_PROJECT_ID') || process.env.BLOCKFROST_PROJECT_ID;
        const appUrl = this.configService.get('APP_URL');
        const blockfrostUrl = projectId.includes('preview')
            ? 'https://cardano-preview.blockfrost.io/api/v0'
            : 'https://cardano-mainnet.blockfrost.io/api/v0';
        const cardanoNetwork = projectId.includes('preview')
            ? 'Preview'
            : 'Mainnet';
    
        const lucid = await Lucid.new(
            new Blockfrost(blockfrostUrl, projectId),
            cardanoNetwork,
        );
    
        return [lucid, projectId, blockfrostUrl, cardanoNetwork, appUrl];
    }
}