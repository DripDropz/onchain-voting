# Feature Documentation

## Table of Contents
1. Ballots  
    * Purpose  
    * Feature Description  
    * How to Use  
        1. Creating a snapshot  
        2. Creating a Ballot  
        3. Voting on a Ballot  

# Ballots

## Feature Description
A ballot, stored on-chain, is an official vote submission. It contains your cryptographically signed ranked choices for proposals or candidates. Voting power is automatically calculated based on the ADA staked at the snapshot so no funds need to be sent.

Once signed, your ballot is recorded directly on the Cardano blockchain. This makes it secure, transparent, and independently verifiable by anyone.

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
This feature gives Cardano communities, DAOs, and projects a transparent secure on-chain voting mechanism that reflects the voice of real ADA holders. It is useful for community elections, governance decisions, proposal prioritization, and structured feedback.

## How to Use
Only admins may create new snapshots and ballots.

---

# Creating a Snapshot

Go to the admin dashboard and select “New Ballot.”

Add a title and description, set the policyID to lovelace, set the type to file, set the status to published, then select Create.

![Snapshot Upload](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots1.jpg)

You can now upload your snapshot file.

![Snapshot Confirm](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots2.jpg)

Select Confirm under the preview table to generate users and voting power. Each row links to the snapshot. A wallet’s stake key will later match the user to their voting power once they connect to vote.

---

# Creating a Ballot

On the admin dashboard select “New Ballot.”  
The form captures all required fields as defined in `doc/schema/Ballot.json`. Starttime, endtime, and version are included. Questions can be added after submitting the form. The checklist on the right indicates missing steps.

1. Fill in the form and submit.

![Ballot Form](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots3.jpg)

2. Select Add Question. Questions follow the schema in `doc/schema/Question.json`.

![Add Question](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots4.jpg)

3. Add choices to the question.

![Choices](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots5.jpg)

4. Attach the snapshot to your ballot.

![Attach Snapshot](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots6.jpg)

5. Add policies to your ballot. Policies derive from wallets you import or create. Seedphrases are hashed and stored internally. Wallets must contain enough ADA to cover minting costs.

![Policy Wallet](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots7.jpg)

Once policies are added you may upload an image used on the token.

![Policy Image](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots8.jpg)

6. Publish your ballot using the “Publish Ballot” button.

![Publish](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots9.jpg)

Once published your ballot becomes available for voting.

---

# Voting on the Ballot

Navigate to the ballot list and select View on any open ballot.

![Open Ballots](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots10.jpg)

1. Register to vote. A registration token will be sent to your wallet.

![Register](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots11.jpg)

2. Once registered you may cast your vote. Select your choices and sign the transaction on-chain. A voting token will be sent to you with your selections as metadata.

![Vote](https://github.com/DripDropz/onchain-voting/blob/main/docs/images/ballots12.jpg)
