declare namespace App.DataTransferObjects {
export type BallotData = {
hash: string | null;
title: string;
description?: string | null;
version?: string | null;
status: string | null;
live: boolean | null;
open: boolean | null;
type: string | null;
created_at?: string | null;
updated_at?: string | null;
started_at?: string | null;
ended_at?: string | null;
total_votes?: any;
snapshot?: App.DataTransferObjects.SnapshotData | null;
user: App.DataTransferObjects.UserData | null;
questions: Array<App.DataTransferObjects.QuestionData> | null;
policies: Array<App.DataTransferObjects.PolicyData> | null;
voters?: Array<App.DataTransferObjects.VoterData> | null;
responses?: any | null;
user_responses?: Array<App.DataTransferObjects.BallotResponseData> | null;
votes?: Array<App.DataTransferObjects.VoteData> | null;
tokens?: Array<App.DataTransferObjects.TokenData> | null;
txs?: Array<App.DataTransferObjects.TxData> | null;
};
export type BallotResponseData = {
hash: string | null;
created_at?: string | null;
ballot: App.DataTransferObjects.BallotData | null;
question: App.DataTransferObjects.QuestionData | null;
choices?: Array<App.DataTransferObjects.QuestionChoiceData> | null;
user: App.DataTransferObjects.UserData | null;
voting_power: App.DataTransferObjects.VotingPowerData | null;
submit_tx: string | null;
rank: number | null;
};
export type CardanoKey = {
type: string;
description: string;
cborHex: string;
};
export type CategoryData = {
title: string | null;
description: string | null;
id: number | null;
};
export type CrumbData = {
label: string | null;
link: string | null;
external?: boolean | null;
};
export type PetitionData = {
hash: string | null;
title: string;
description?: string | null;
user: App.DataTransferObjects.UserData | null;
user_id: number | null;
created_at?: string | null;
updated_at?: string | null;
started_at?: string | null;
ended_at?: string | null;
image_url?: string | null;
signatures_count?: number | null;
status: string;
categories: Array<App.DataTransferObjects.CategoryData> | null;
ballot: App.DataTransferObjects.BallotData | null;
rules: Array<App.DataTransferObjects.RuleData> | null;
petition_goals: any;
};
export type PolicyData = {
hash: string | null;
script: Array<any>;
policy_id: string | null;
context: string | null;
created_at?: string | null;
image_link?: string | null;
wallet_balance: number | null;
wallet_funded: boolean | null;
};
export type PollData = {
hash: string | null;
id: number | null;
title: string;
description?: string | null;
publish_on_chain?: boolean | null;
responses_count: number | null;
user: App.DataTransferObjects.UserData | null;
created_at?: string | null;
updated_at?: string | null;
status: string;
question: App.DataTransferObjects.QuestionData | null;
rules: Array<App.DataTransferObjects.RuleData> | null;
ballot: App.DataTransferObjects.BallotData | null;
user_responses?: Array<App.DataTransferObjects.QuestionResponseData> | null;
};
export type QuestionChoiceData = {
hash: string | null;
title: string;
description?: string | null;
selected?: boolean | null;
created_at?: string | null;
question: App.DataTransferObjects.QuestionData | null;
ballot: App.DataTransferObjects.BallotData | null;
order: number | null;
};
export type QuestionData = {
hash: string | null;
title: string;
description?: string | null;
supplemental?: string | null;
max_choices?: number | null;
created_at?: string | null;
status: string;
type: string;
user: App.DataTransferObjects.UserData | null;
ballot: App.DataTransferObjects.BallotData | null;
choices: Array<App.DataTransferObjects.QuestionChoiceData> | null;
ranked_user_responses?: Array<App.DataTransferObjects.BallotResponseData> | null;
choices_tally: Array<any> | null;
};
export type QuestionResponseData = {
hash: string | null;
model_type?: string | null;
model_id?: number | null;
created_at?: string | null;
question: App.DataTransferObjects.QuestionData | null;
choices?: Array<App.DataTransferObjects.QuestionChoiceData> | null;
user: App.DataTransferObjects.UserData | null;
voting_power: App.DataTransferObjects.VotingPowerData | null;
submit_tx: string | null;
rank: number | null;
};
export type RegistrationData = {
hash: string;
power: number;
token?: App.DataTransferObjects.TokenData | null;
};
export type RuleData = {
hash: string | null;
title: string;
description?: string | null;
type: string | null;
operator: string | null;
value1: string | null;
value2: string | null;
};
export type SignatureData = {
hash: string;
email_signature: string | null;
wallet_signature: string | null;
created_at: string | null;
stake_address: string | null;
voter: App.DataTransferObjects.VoterData | null;
ballot: App.DataTransferObjects.BallotData | null;
pollData: App.DataTransferObjects.PollData | null;
petition: App.DataTransferObjects.PetitionData | null;
};
export type SigningKey = {
type: string;
description: string;
cborHex: string;
};
export type SnapshotData = {
hash: string | null;
title: string;
description?: string | null;
user: App.DataTransferObjects.UserData | null;
ballot?: App.DataTransferObjects.BallotData | null;
created_at?: string | null;
updated_at?: string | null;
policy_id?: string | null;
type?: string | null;
status: string;
voting_powers?: App.DataTransferObjects.VotingPowerData | null;
has_voting_powers: boolean | null;
metadata: Array<any> | null;
};
export type TokenData = {
hash: string;
policy_id: string;
ballot?: App.DataTransferObjects.BallotData | null;
voter: App.DataTransferObjects.VoterData;
};
export type TxData = {
};
export type UserData = {
hash: string;
id: number;
name: string;
voter_id: string | null;
hero?: string | null;
ballots?: Array<any> | null;
user_roles: Array<any> | null;
};
export type VerificationKey = {
type: string;
description: string;
cborHex: string;
};
export type VoteData = {
hash: string;
power: number | null;
token: App.DataTransferObjects.TokenData | null;
voter: App.DataTransferObjects.VoterData;
ballot: App.DataTransferObjects.BallotData | null;
};
export type VoterData = {
voter_id: string;
voting_power: any;
votes?: App.DataTransferObjects.VoteData | null;
registrations?: App.DataTransferObjects.RegistrationData | null;
tokens?: App.DataTransferObjects.TokenData | null;
txs?: App.DataTransferObjects.TxData | null;
};
export type VotingPowerData = {
hash: string | null;
user: App.DataTransferObjects.UserData | null;
snapshot: App.DataTransferObjects.SnapshotData | null;
voting_power: number;
created_at?: string | null;
};
}
