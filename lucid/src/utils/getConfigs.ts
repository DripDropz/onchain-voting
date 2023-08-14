import { Blockfrost, Lucid } from "lucid-cardano";
import { Request } from "express";

const getConfigs = async (request: Request): Promise<[Lucid, string, string, string]> =>  {
    const projectId = process.env.BLOCKFROST_PROJECT_ID;    
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

export default getConfigs;