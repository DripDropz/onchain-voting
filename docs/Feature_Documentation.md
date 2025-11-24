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
A ballot, stored on-chain, is an official vote submission. It contains your cryptographically signed ranked choices for proposals or candidates. Voting power is automatically calculated based on the ADA staked at the snapshot so no funds need to be sent.

Once signed, your ballot is recorded directly on the Cardano blockchain. This makes it secure, transparent and independently verifiable by anyone.

Ballots are tied to a snapshot to ensure fairness and transparency.

## What Is a Snapshot
A snapshot captures the state of the Cardano blockchain at a specific block height, recording the ADA balances delegated to stake pools across all wallets. This determines each voter’s voting power for a specific ballot.

### Why Snapshots Matter
* Prevents manipulation by fixing voting power at a specific time  
* Ensures fairness because only wallets with at least 1 ADA at the snapshot are eligible  
* Transparency because snapshot data can be checked publicly even if not stored directly on-chain  

## Ballot Policies
Two policies support the governance mechanism.

1. Registration Token Policy  
   This mints a registration token for each eligible wallet. This token is required for voting.
2. Voting Token Policy  
   This mints a voting token after a successful vote and burns the registration token. Your choices appear as metadata on the voting token so anyone can verify them on-chain.

## Purpose
This feature gives Cardano communities, DAOs and projects a transparent secure on-chain voting mechanism that reflects the voice of real ADA holders. It is useful for community elections, governance decisions, proposal prioritization and structured feedback.

## How to Use
Only admins may create new snapshots and ballots.

---
# Creating a Snapshot

Go to the admin dashboard and select “New Ballot”.

Add a title and description, set the policyID to lovelace, set the type to file, set the status to published then select Create.

![Snapshot Upload](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots1.jpg)

You can now upload your snapshot file.

![Snapshot Confirm](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots2.jpg)

Select "Confirm" under the preview table to generate users and voting power.

---
# Creating a Ballot

1. Fill in the form and submit.

![Ballot Form](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots3.jpg)

2. Select "Add Question".

![Add Question](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots4.jpg)

3. Add choices.

![Choices](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots5.jpg)

4. Attach the snapshot.

![Attach Snapshot](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots6.jpg)

5. Add policies.

![Policies](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots7.jpg)

6. Upload an image for the token.

![Token Image](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots8.jpg)

7. Publish.

![Publish](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots9.jpg)

---
# Voting on a Ballot

![Open Ballots](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots10.jpg)

1. Register to vote.

![Register](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots11.jpg)

2. Cast your vote.

![Vote](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots12.jpg)

---
# 2. Petitions

## Purpose
Petitions allow users to propose changes or voice concerns about the ecosystem.

## Feature Description
Petitions enable users to initiate change, gather support and surface high-interest issues. They are fully verifiable on-chain and can require NFTs, FTs or staking conditions.

---
## How to Use

### Creating a Petition

1. Navigate to the Petitions section of the app.

2. Click “Create New Petition”.

![Petition New](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/petitions1.jpg)

3. Click “Yes, that works”.

![Confirm Modal](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/petitions2.jpg)

4. Fill in the required fields.

![Title Field](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/petitions3.jpg)

![Description Field](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/petitions4.jpg)

5. Save the petition.

![Save Petition](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/petitions5.jpg)

6. Access the petition management page.

![Manage Petition](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/petitions6.jpg)

---
## Admin Role in Petition Creation

Admins add signature goals that control visibility, featured status and eligibility to become a ballot.

![Admin Settings](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/petitions7.jpg)

![Signature Goals](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/petitions8.jpg)

---
## Signing a Petition

Petitions may be signed:

1. By email  
2. With a wallet  

![Sign Petition](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/petitions9.jpg)

If a token requirement exists, email is disabled.

![Token Requirement](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/petitions10.jpg)

Creators can track progress:

![Petition Overview](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/petitions11.jpg)

---
# 3. Polls

## Feature Description
Polls are multiple choice questions answered using Cardano wallets. Each vote is authenticated and recorded to ensure transparency and auditability. Polls typically involve choosing one or more predefined responses.

Polls can:
* Help guide project decisions.
* Collect feedback on proposed features.
* Measure community consensus on upcoming changes.

## Purpose
Polls allow fast structured input collection and deliver transparent feedback and community sentiment secured by blockchain verification.

---
## How to Use

### Creating a Poll

1. Navigate to the Polls section.

![Polls List](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/polls1.jpg)

2. Click “Create Poll”.

![Create Poll](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/polls2.jpg)

3. Fill in the required fields.

![Poll Question](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/polls3.jpg)

4. Define who can respond by attaching an FT or NFT.

![Poll Criteria](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/polls4.jpg)

5. Publish the poll. After admin approval it will be live.

---
## Participating in a Poll

Once approved the poll appears in the browse, active and answered tabs.

![Poll Participate](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/polls5.jpg)
