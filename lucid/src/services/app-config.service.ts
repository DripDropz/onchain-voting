import { Blockfrost, Lucid } from "lucid-cardano";
import { Request } from "express";
import { Injectable } from "@nestjs/common";
import { ConfigService } from "@nestjs/config";

@Injectable()
export class AppConfigService {
    constructor(
        private configService: ConfigService
    ) { }

    async getConfigs(request: Request): Promise<[Lucid, string, string, string]> {
        const projectId = this.configService.get('BLOCKFROST_PROJECT_ID') || process.env.BLOCKFROST_PROJECT_ID;
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
    
        return [lucid, projectId, blockfrostUrl, cardanoNetwork];
    }
}