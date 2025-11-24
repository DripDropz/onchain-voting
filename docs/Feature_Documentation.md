# Feature Documentation

## Table of Contents
1. Ballots  
    * Purpose  
    * Feature Description  
    * How to Use  
        1. Creating a snapshot  
        2. Creating a Ballot  
        3. Voting on a Ballot
2. Petitions  
    * Purpose  
    * Feature Description  
    * How to Use  
        1. Creating a Petition  
        2. Signing a Petition
3. Polls  
    * Purpose  
    * Feature Description  
    * How to Use  
        1. Creating a Poll  
        2. Participating in a Poll

# 1. Ballots

## Feature Description
A ballot, which is stored on-chain, is an official vote submission. It contains your cryptographically signed ranked choices for proposals or candidates. Voting power is automatically calculated based on the ADA staked at the snapshot. There is no need to send funds. Once signed, your ballot is recorded directly on the Cardano blockchain. This ensures your vote is secure, transparent and independently verifiable by anyone. Ballots are tied to a snapshot to ensure fairness and transparency in the voting process.

## What Is a Snapshot
A snapshot captures the state of the Cardano blockchain at a specific block height. It records the ADA balances delegated to stake pools across all wallets. This snapshot determines each voter’s voting power for a particular ballot.

### Why Snapshots Matter
* Prevents manipulation. By fixing voting power at the snapshot time it prevents users from acquiring additional ADA after the snapshot to influence the vote.  
* Ensures fairness. Only wallets holding at least 1 ADA at the snapshot are eligible to vote.  
* Transparency. The snapshot is based on a public record so anyone can double-check it even though it is not saved directly on the blockchain.  

## Ballot Policies
Two distinct policies support ballots.

1. Registration token policy. This mints a registration token for each eligible wallet as a prerequisite to vote.  
2. Voting token policy. This mints a voting token after a successful vote and burns the registration token. Choices appear as metadata and can be verified on-chain.  

## Purpose
This feature empowers Cardano communities, DAOs and projects with a transparent secure on-chain voting mechanism that captures the voice of real ADA holders. It enables informed decision-making, community feedback, elections, proposal prioritization and any governance process that benefits from decentralized consensus.

* Community elections  
* Proposal prioritization  
* Governance decisions  
* Feedback collection  

# How to Use

### Creating a Snapshot
Admins add snapshots. Go to the admin dashboard and select New Ballot. Add a title and description. Ensure the policyID is lovelace, the type is file and the status is published then select Create.

![Snapshot Upload](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots1.jpg)

Upload the snapshot file then select Confirm under the preview table. This generates users and voting power and links them to the snapshot.

![Snapshot Confirm](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots2.jpg)

![Ballot Form](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots3.jpg)

### Creating a Ballot
On the admin dashboard select New Ballot. Starttime, endtime and version are on the form and questions can be added afterwards.

1. Fill in the form.

![Add Question](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots4.jpg)

2. Select Add Question.

![Choices](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots5.jpg)

3. Add choices.

![Attach Snapshot](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots6.jpg)

4. Attach a snapshot.

![Policies](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots7.jpg)

5. Add the policies. Wallets must have funds to cover minting costs.

![Token Image](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots8.jpg)

6. Add an image for the token.

![Publish](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots9.jpg)

7. Publish your ballot.

![Open Ballots](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots10.jpg)

### Voting on a Ballot
Navigate to the ballot page to view open ballots.

![Register](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots11.jpg)

1. Register to vote. A registration token will be sent to your wallet.

![Vote](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots12.jpg)

2. Cast your vote. A voting token with metadata is sent after signing.

# 2. Petitions

## Purpose
Petitions are community-driven initiatives that allow users to propose changes or voice concerns about various aspects of the ecosystem or specific communities.

## Feature Description
The Petitions feature empowers users to initiate change, raise issues or gather community support on important topics without waiting for official action. By allowing verified wallet holders to create and sign petitions this feature promotes transparency and accountability, encourages bottom-up decision-making, filters community interest, strengthens governance and allows targeted participation using staking, NFTs or FTs.

Petitions may include a signature threshold, a deadline or both.

## How to Use

### Creating a Petition

1. Navigate to the Petitions section of the app.

![Petition New](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/petitions1.jpg)

2. Select Create New Petition.

![Confirm Modal](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/petitions2.jpg)

3. Select Yes, that works.

![Title Field](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/petitions3.jpg)

4. Fill in the required fields.

![Description Field](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/petitions4.jpg)

![Save Petition](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/petitions5.jpg)

5. Select Save Petition.

![Manage Petition](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/petitions6.jpg)

6. After saving you can access the petition manage page to edit the petition and set token criteria.

![Admin Settings](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/petitions7.jpg)

### Admin Role
For the petition to be available the admin must add signature goals. These determine how the petition advances, its visibility and its eligibility for becoming a ballot.

![Signature Goals](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/petitions8.jpg)

Signature goals include:

* Visibility threshold for appearing on the site  
* Featured petition threshold  
* Ballot eligibility threshold  

![Sign Petition](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/petitions9.jpg)

### Signing a Petition
Petition creators can share a link to collect signatures. If the petition meets the visibility threshold it appears in the active list.

Signatures can be collected by:

1. Email for petitions without a token requirement  
2. Wallet signatures through a browser extension  

If a token requirement exists the email option is disabled.

![Sign Petition](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/petitions10.jpg)

Creators can track petition progress.

![Petition Overview](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/petitions11.jpg)

# 3. Polls

## Feature Description
Polls are multiple-choice questions answered using Cardano wallets. Each vote is authenticated and recorded to ensure transparency and auditability. Polls help guide decisions, collect feedback and measure community sentiment.

## Purpose
Polls allow communities to collect fast structured input. Polls support transparent feedback, pulse checks and sentiment analysis securely recorded on-chain.

## How to Use

### Creating a Poll

1. Navigate to the Polls section.  
2. Select Create Poll.

![Poll Overview](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/polls1.jpg)

3. Fill in the required fields for the poll question.

![Poll Question](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/polls2.jpg)

4. Define who can respond by attaching an FT or NFT.

![Poll Criteria](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/polls3.jpg)

5. Publish the poll. On admin approval the poll becomes live.

![Poll Participate](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/polls4.jpg)

### Participating in a Poll
Once approved the poll appears in the browse, active and answered tabs.

![Poll Participate](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/polls5.jpg)
