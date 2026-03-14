declare namespace App.DataTransferObjects {
export type BallotData = {
hash: string | null;
title: string;
description?: string;
version?: string;
status: string | null;
live: boolean | null;
open: boolean | null;
type: string | null;
created_at?: string;
updated_at?: string;
started_at?: string;
ended_at?: string;
total_votes?: any;
snapshot?: App.DataTransferObjects.SnapshotData;
user: App.DataTransferObjects.UserData | null;
questions: Array<App.DataTransferObjects.QuestionData> | null;
policies: Array<App.DataTransferObjects.PolicyData> | null;
voters?: Array<App.DataTransferObjects.VoterData>;
responses?: any;
user_responses?: Array<App.DataTransferObjects.BallotResponseData>;
votes?: Array<App.DataTransferObjects.VoteData>;
tokens?: Array<App.DataTransferObjects.TokenData>;
txs?: Array<App.DataTransferObjects.TxData>;
};
export type BallotResponseData = {
hash: string | null;
created_at?: string;
ballot: App.DataTransferObjects.BallotData | null;
question: App.DataTransferObjects.QuestionData | null;
choices?: Array<App.DataTransferObjects.QuestionChoiceData>;
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
external?: boolean;
};
export type PetitionData = {
hash: string | null;
title: string;
description?: string;
user: App.DataTransferObjects.UserData | null;
user_id: number | null;
created_at?: string;
updated_at?: string;
started_at?: string;
ended_at?: string;
image_url?: string;
signatures_count?: number;
status: string;
categories: Array<App.DataTransferObjects.CategoryData> | null;
ballot: App.DataTransferObjects.BallotData | null;
rules: Array<App.DataTransferObjects.RuleData> | null;
petition_goals: any;
is_visible: boolean | null;
is_featured: boolean | null;
};
export type PolicyData = {
hash: string | null;
script: Array<any>;
policy_id: string | null;
context: string | null;
created_at?: string;
image_link?: string;
wallet_balance: number | null;
wallet_funded: boolean | null;
};
export type PollData = {
hash: string | null;
id: number | null;
title: string;
description?: string;
publish_on_chain?: boolean;
responses_count: number | null;
user: App.DataTransferObjects.UserData | null;
user_id: number | null;
created_at?: string;
updated_at?: string;
started_at?: string;
ended_at?: string;
image_url?: string;
status: string;
question: App.DataTransferObjects.QuestionData | null;
rules: Array<App.DataTransferObjects.RuleData> | null;
ballot: App.DataTransferObjects.BallotData | null;
user_responses?: Array<App.DataTransferObjects.QuestionResponseData>;
};
export type QuestionChoiceData = {
hash: string | null;
title: string;
description?: string;
selected?: boolean;
created_at?: string;
question: App.DataTransferObjects.QuestionData | null;
ballot: App.DataTransferObjects.BallotData | null;
order: number | null;
responses_count?: number;
};
export type QuestionData = {
hash: string | null;
title: string;
description?: string;
supplemental?: string;
max_choices?: number;
created_at?: string;
status: string;
type: string;
user: App.DataTransferObjects.UserData | null;
ballot: App.DataTransferObjects.BallotData | null;
choices: Array<App.DataTransferObjects.QuestionChoiceData> | null;
ranked_user_responses?: Array<App.DataTransferObjects.BallotResponseData>;
choices_tally: Array<any> | null;
};
export type QuestionResponseData = {
hash: string | null;
model_type?: string;
model_id?: number;
created_at?: string;
question: App.DataTransferObjects.QuestionData | null;
choices?: Array<App.DataTransferObjects.QuestionChoiceData>;
user: App.DataTransferObjects.UserData | null;
voting_power: App.DataTransferObjects.VotingPowerData | null;
submit_tx: string | null;
rank: number | null;
};
export type RegistrationData = {
hash: string;
power: number;
token?: App.DataTransferObjects.TokenData;
};
export type RuleData = {
hash: string | null;
title: string;
description?: string;
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
description?: string;
user: App.DataTransferObjects.UserData | null;
ballot?: App.DataTransferObjects.BallotData;
created_at?: string;
updated_at?: string;
policy_id?: string;
type?: string;
status: string;
voting_powers?: App.DataTransferObjects.VotingPowerData;
has_voting_powers: boolean | null;
metadata: Array<any> | null;
};
export type TokenData = {
hash: string;
policy_id: string;
ballot?: App.DataTransferObjects.BallotData;
voter: App.DataTransferObjects.VoterData;
};
export type TxData = {
};
export type UserData = {
hash: string;
id: number;
name: string;
voter_id: string | null;
hero?: string;
ballots?: Array<any>;
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
votes?: App.DataTransferObjects.VoteData;
registrations?: App.DataTransferObjects.RegistrationData;
tokens?: App.DataTransferObjects.TokenData;
txs?: App.DataTransferObjects.TxData;
};
export type VotingPowerData = {
hash: string | null;
user: App.DataTransferObjects.UserData | null;
snapshot: App.DataTransferObjects.SnapshotData | null;
voting_power: number;
created_at?: string;
};
}
