# DripDropz On-Chain Voting Schema #

This directory contains the schema files describing the format for submitting votes to the chain via the DripDropz
On-Chain Voting tool format. A [Question](Question.json) is a sub-schema that describes a specific part of the 
[Ballot](Ballot.json) format. A [Vote](Vote.json) represents a single, individual user vote that is cast onto the chain
and is the part that is tabulated to determine results.

**Note: All submissions of metadata on-chain should use 446 for the metadata index. The initial ballot should be
published with a nameless token under the voting policy and voter ballots should be submitted (one or more at a time)
using a token with "Ballot" as the Asset ID.**

## Schema Notes and Considerations ##

### Ballot.json ###

The ballot in this case refers to the document providing a voter with their options of who or what they are voting for.
The composed JSON file describing the ballot to be voted upon by voters should be recorded to the chain in advance of
the vote occurring and should use a nameless token from the generated voting simple native script policy. This is
important for legacy interpreters to have, not only a record of what votes were cast by whom, but what were the questions,
how were they posed to voters, what were the available candidate options, etc. Casting the initial "Ballot" onto the
chain also allows us to avoid data duplication and minimize the size of individual voter "ballots" (cast votes) by
referencing questions and candidates by numerical index, providing a substantially smaller on-chain data footprint than
other alternatives.

#### Version ####

The version field of the Ballot serves to inform independent auditors and future data historians as to what the expected
formats and available functionalities are. Including semantic versioning from the beginning allows us to treat this as
a living document that can grow and adapt as new methods, concepts, and mechanics are incorporated.

#### Type ####

This field identifies the mechanism which determines voter eligibility for participation. As of v1.0.0 of this library
the only supported option is "snapshot" although this may be changed to other versions such as submitting voter
registration rolls to chain or similar in the future.

#### Snapshot ####

This object provides a variety of details for a "type" === "snapshot" vote that determine the criteria for qualification
to vote. This object will hopefully provide enough detail to provide a third party auditor with enough information to
conduct their own blockchain ledger snapshot and compare and confirm things such as voter eligibility, voter power, etc.

#### Authority ####

As of v1.0.0 this is simply a text field identifying the "Voting Authority", the party responsible for hosting and
ultimately certifying and ratifying the results of the vote. This will likely be expanded in the future to provide more
context and proof of the authenticity of the voting authority.

#### Rules ####

This object is new as part of the movement to open source this voting schema and does not exist in currently extant 
ballots that have previously been cast on chain. Again the schema design has been geared towards logical clustering
and future expansion here, so it is quite likely that additional rules and toggles will be added to this section in
the future.

- **useVoterPower**: Does this vote use "voter power" based on the concept of holding more coins/tokens/shares at the
  time of the snapshot or is it designed to be a one wallet/person/entity per vote.
- **capVoterPower**: For votes utilizing voter power, it may be desirable to set a certain cap or threshold maximum
  at which point a voter does not receive additional voting power even if they own additional coins/tokens/shares.
  In this case, this value should be set to the upper bound and no shareholder should receive more than this amount
  of voting power.
- **allowTransfer**: This system utilizes the idea of an on-chain, voter registration native asset to support older,
  legacy wallets such as Daedalus or Stake Pool Operator (SPO) command-line wallets to participate in the governance
  process. Because these are self-custody native assets that users must return as part of casting their vote, there
  is both the potential for these tokens to be used to "delegate" a wallet's voting power to another individual and
  also for users to "sell" their voting power. Depending upon the needs and desires of the voting authority, this 
  behavior could be either a benefit or a detriment. As such, this rule is introduced to indicate whether 
  transferred voting power tokens should be accepted as valid votes via the voting authority.

#### Start/End ####

These are relatively straightforward Unix timestamp value (seconds since the epoch beginning 1970-01-01 00:00:00 UTC)
that represent the period of time that is considered the "Voting Window". An important note is that votes cast on-chain
prior to the voting window (Start) should be ignored/discarded while some votes may be cast after the voting window 
ends if the voting authority chooses to honor any and all votes that were "in line" prior to the voting window ending.
Certainly there will be societal norms of accepted best practice and what counts as a "reasonable" amount of time after
the voting window has closed for all votes to be cast on-chain and the vote results to be considered forever locked.

### Questions [Questions.json] ###

The Questions portion of the Ballot file should consist of an array of one or more Questions (a separate schema object
defined in [Questions.json](Question.json)). Questions should follow the format included in this document again for the
primary purpose of allowing 3rd party auditors and future data historians to rebuild an accurate portrait of the vote as
it was presented to voters at the time.

The schema format for questions on both voter ballot submissions and the on-chain ballot descriptor have been 
constructed to support a variety of question and answer formats while using the same reusable and extensible base model.

**Note:** The model described herein requires an expectation that any voter may choose to "abstain" from any ballot
choice or question. As such, a result of either 0 (zero) or null (blank) should always be processed as a voter abstention.

#### Question Type ####

Currently v1.0.0 of this document supports the following question types on a given ballot: single choice, multiple choice,
or ranked choice.

- **Single**: The user is able to choose only one of the options. Single Choice questions can be used for boolean 
  (true/false) questions or to choose a single candidate from a list of candidate options. Example: _Should we spend money
  from the treasury? Yes or No?_
- **Multiple**: The user can select none, one or more [choices](#choices) up to [maxChoices](#maxchoices) as defined
  within the question rules. Example: _Choose your three (3) favorite pizza toppings_.
- **Ranked**: Ranked choice voting is a fairly complex voting format, but we have implemented a relatively simplistic
  model for the purposes of v1.0.0 of this voting platform. Functionally to the user, a _Ranked Choice_ vote is identical
  to a multiple choice question with the exception that the user is expected to sort their selections in order of
  preference.
 
  The primary difference here is in the method of tabulating the result set. Where a multiple choice question applies 
  voter power to all _X_ choices of the user evenly; in a _Ranked Choice_ vote the voter's first choice receives votes 
  equal to 100% of the voter's "power" and subsequent choices receive a decreasing amount of voting power.

  **The Math**: A candidate receives... 

  _voter_power_ × (1 - ((_candidate_choice_index_ - 1) ÷ _maxChoices_))
  
  First Choice: `100 × (1 - ((1 - 1) ÷ 5))` = 100% Vote Power

  Second Choice: `100 × (1 - ((2 - 1) ÷ 5))` = 80% Vote Power

  Third Choice: `100 × (1 - ((3 - 1) ÷ 5))` = 60% Vote Power

  And so on depending upon the number of choices and _maxChoices_ given to a voter.
  
#### Question ####

Another relatively self-explanatory one, but this is the actual, human-readable question/proposition proposed to voters
to make a choice on. For the sake of optimizing on-chain storage space we recommend the question be kept as brief as 
possible (while maintaining a clear and full understanding the user's choice) and any additional errata should be 
included in the [supplemental](#supplemental) field.

#### Choices ####

Choices represent the user's choices on a given ballot question. This should be an array of either strings or string
arrays if you need more than 64 characters.

**Important Note**: "Abstain" is always presumed to be an option and should not be explicitly declared in the choices
array. For this reason, when submitting and tabulating votes, index 0 (zero) is always assigned to "abstain" and entries
in the choices array are subsequently 1-indexed in all other references.

#### Supplemental ####

This field is optional and should be a URI referencing additional information about this particular question. We
recommend using Arweave or IPFS to store ballot supplemental data.

If using IPFS the URI should be formatted as `ipfs://[file_cid]` if using Arweave the URI should be formatted as
`ar://[txn_id]`. Although we do not recommend it, traditional URLs can be used and should be included along with
the http/https prefix like `https://example.com/ballot5/question3/supplemental/`.

#### MaxChoices ####

This field should only be present for multiple and ranked choice type questions and represents the maximum amount
of choices that a user is allowed to select and submit as part of their ballot. For multiple or ranked choice 
questions where a user should have the option to select all candidates you can also exclude this field and maxChoices
is assumed to default to the number of entries in the choices array.

### Vote.json ###

This file describes the format of an individual user vote to be cast onto the blockchain on behalf of a user. The schema
was intentionally optimized to minimize the size of individual user vote (ballot) submissions in order to facilitate 
batching by the Voting Authority.

#### Voter_ID ####

This is the unique voter ID of the person casting their vote. In most cases this will be a wallet stake key but future
versions may allow other means of voting (i.e. oAuth login, etc).

#### Voter_Power ####

If voter power is being used for a vote this field should contain the amount of voting power/weight granted to the 
particular Voter_ID. This should be verifiable by independent auditors via following the rules in the 
[Ballot.Snapshot](#snapshot).

#### Ballot_ID ####

This should be a uniquely generated hash (usually of the Voter_ID + choices) so that individual voters can confirm that
their ballot was submitted to the chain without tampering or modification by the Voting Authority.

#### Signature ####

This field has not been used in practice yet but is reserved in the schema to support voters casting their ballot via
providing a signature from their wallet pursuant to CIP-8/CIP-30 light wallet connectors. Initial thinking is that the
signature key should match the Voter_ID and the signature payload should match the Ballot_ID.

#### Voter Choices ####

Choices should consist of an array of arrays with integer values representing a user's vote selections. The index of
an entry in the Voter.Choices array should correspond to the same numeric index as specified in Ballot.Questions. The
integer value(s) specified within a Voter.Choices entry should correspond to the 1-indexed entries in the 
Question.Choices. A value of 0 or empty (null) within a Voter.Choices entry should be inferred as abstaining from that
question.