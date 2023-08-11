const blockfrostRequest = async ({
    body = null,
    endpoint = '',
    headers = {},
    method = 'GET'
}) => {
    let networkEndpoint = process.env.NETWORK === '0' ? 'https://cardano-preview.blockfrost.io/api/v0' : 'https://cardano-mainnet.blockfrost.io/api/v0' //process.env.BLOCKFROST_URL ? process.env.BLOCKFROST_URL : ''
    let blockfrostApiKey = process.env.NETWORK === '0' ? 'preview2QfIR5epKjaFmh54Id75yXAM7yStk3vc' : 'preview2QfIR5epKjaFmh54Id75yXAM7yStk3vc';

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
    } catch (error) {
        return error;
    }
}

export default blockfrostRequest