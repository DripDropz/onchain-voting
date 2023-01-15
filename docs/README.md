# DripDropz On-Chain Voting Documentation #

## Technical Documentation ##

### Process Methodology ###

As stated elsewhere, the goal of this voting system has been, from the beginning Auditability, Security, and Ease of Use.
With that in mind we developed the following process methodology as described below to ensure that we could support the
widest array of possible voter edge cases to ensure maximum voter participation while maintaining the integrity of the
vote results and public auditability.

#### Step #1: Ensure All Data is Publicly Available to All ####

The first priority of the voting system was ensuring that there was complete end-to-end, public visibility into all vote
data. To that end and building upon earlier work on the SPOCRA voting system, we have slightly refactored and expanded
the format to optimize for ledger space and auditability.

With the advent of native tokens in the Cardano ecosystem we have chosen to adopt these in the name of making it easier
than ever to track and record specific votes in specific governance votes without placing undue burden on auditors. To
this end, all on-chain metadata published regarding a particular vote should be published by a discrete policy so that
the count of voters, their votes, and the results are easily indexed in the ledger.

On-Chain Metadata Schemas are documented in the [schema](schema) directory.

#### Step #2: Ensure Equality and Ease of Access ####

In order to maximize voter participation, particularly for votes based on the amount of ADA controlled, we must ensure
that all holders and wallets may participate equally. To this end, we needed a voting system that could support
all types of Cardano wallets from command line wallets (mostly stake pool operators), light wallets (Eternl, Nami, etc),
and full-node wallets (Daedalus).

To this end, we crafted the system in such a way that the voting authority hosts a server where voters may come to 
register their intent to vote (more on this later) and ballot choices. Voters then send a transaction from their wallet
 and receive a "Voter Registration Token" in return. This token can only be received and later returned by the wallet
holder in question in order to confirm their vote but all different types of wallets in the current ecosystem can
participate equally and fairly.

#### Step #3: Security ####

Arguably, the most important point of any governance or voting system is that those participating in it must have faith
(trust) in the truthfulness of the results (whether their team wins or not). To that end, we address security last in 
this list but arguably as the most significant point.

We must trust that every participating actor (usually in our case a specific wallet on the blockchain ledger) has only
participated once in the course of the governance vote. We must also have trust that votes were recorded honestly and
without the possibility of tampering.

There are several baked-in integrity checks via our methodology that can be provably audited using the public ledger of
the blockchain to ensure that participants may have trust in the results:

1. All participating actors must engage in two on-chain transactions via their wallet in order to participate:
   1. A voter registration transaction to indicate intent to cast a vote
   2. A vote submission transaction via the voter registration token to actually cast their vote in a vote
2. Only the true owner of a wallet can submit a legitimate voter registration token, proving their intent to cast their
   vote. (For more details on this please see 
   [Composed Wallet Attack Vector and Defenses](#composed-wallet-attack-vector-and-defenses) below).
3. All voters receive a unique and repeatable hash of their ballot contents prior to submitting their vote. This allows
   individual voters to verify that their vote was not changed between creating their ballot and on-chain submission.

All the above-mentioned security points exist on the public ledger of the blockchain and can be audited by independent
third party auditors without any consent or revelation of information from the voting authority itself making vote 
tampering, modification, or invalid vote counting hopefully non-existent and the veracity of the outcome of the vote
can then be trusted by all participants.

### Lessons Learned ###

#### Composed Wallet Attack Vector and Defenses ####

One of the primary security concerns raised among the team while working on our technical methodology was the danger of
the _Composed Wallet Attack_ (Formerly known as the _FrankenAddress Attack_). 

In this attack the malicious actor can create a _composed_ address consisting of their payment key paired with the 
public staking key of a known wallet with a large amount of assets; creating a hybrid address that is indistinguishable
in most respects from a ledger perspective.

Without any additional security measures in place and simply looking at transactions by address or stake key on the
network it would hypothetically be possible for a malicious actor to hijack the voting power of a large wallet (one
of the ~15M ADA wallets used by the Cardano Foundation for community delegation is a good example). Using only 
single-step address validation and using only the blockchain ledger for proof, it would be possible for the malicious
actor to vote on behalf of this wallet without ever being in possession of the funds.

**Enter the Voter Registration Token**

To combat this particular attack vector we introduced the concept of the voter registration token very specifically.
When a user approaches the voting authority and indicates a desire to vote with their wallet, they are asked to send a
transaction from their wallet to an address that is monitored for transactions and then issues a voter registration 
token in return while recording the staking key of the initiator as well as voting power from the snapshot.

However, returning the voter registration token to the address that sent payment is not enough, as this would send the
voter registration token directly to the malicious user and bypass the proposed security benefits. To do our best on 
the security front it is necessary to go one step further... When a transaction is observed at the voter registration
wallet we identify the staking key of the paying address and then search the blockchain history for the very first 
record of this staking key on the blockchain. We then send the voter registration token back to this original address.

The likelihood of a malicious user being able to register a staking key onto the network before a legitimate user (who
later has enough voting power to be worth the effort of this attack vector) is so infinitesimally small as to be within
our risk tolerance.

Since the legitimate voter must then return their voting registration token in order to cast their vote, this means that
a malicious user could freely pay for any number of "Voter Registration Tokens" but would never have the ability to 
return said token, the attack vector is eliminated.

#### Delegating or Selling Voting Power ####

An unforeseen side effect (benefit?) of introducing the voter registration token was: We introduced a method whereby a
voter could functionally and trustlessly transfer their voting power to another individual or entity to allow that actor
to cast a vote on their behalf.

**The Rise of Vote Delegation or Dark Money At Work?**

There are two sides to this coin:

1. On one hand, a user with any amount of voting power may _delegate_ their token to a trusted _representative_;
   functionally allowing that individual to vote on their behalf.
2. On the other hand, a user may also sell their large, potentially outcome-swaying amount of voting power to the 
   highest bidder.

One function is obviously good (when leveraged by honest participants) while the other is pretty universally agreed to
be a bad and undesirable action.

The DripDropz team, during and following the Catalyst Circle v4 vote hosted using our platform, received a lot of good
feedback about this topic. There are both pros and cons to this functionality, and so we felt it best to leave whether
this behavior would be "banned" into the hands of the Voting Authority conducting a particular vote. 

To that end the schema documentation has been updated and v1.0.0 of this voting platform will provide the configurable
option of whether to accept (or not accept) a vote token from anyone other than the original owner as a configurable and
on-chain field.

### Future Improvements ###

#### Moving Beyond Shareholders ####

At the moment, most blockchain-based voting solutions are very good at providing governance options for what can be seen
as a "shareholder" vote. That is, votes based on the amount of _X_ asset you can verify ownership of via a snapshot of
the blockchain ledger. 

Due to the aforementioned auditability requirements, this means that votes must not be anonymous, making this method
less than ideal for solutions such as government-level elections where voters must be free to vote their conscious free
of the threat of retribution or reprisal.

We believe strongly that the technology will exist in the near future to allow us to reliably and securely conduct votes
on a representative level (i.e. One Person:One Vote) with the advent of DIDs and other advancements.

We will continue to iterate and grow this body of work, share our lessons (painful or otherwise), and endeavour to 
advance the technology and feature set as best we can for the good of the entire community.

