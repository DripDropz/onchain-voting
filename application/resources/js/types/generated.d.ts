declare namespace App.DataTransferObjects {
export type BallotData = {
hash: string | null;
title: string;
description?: string | null;
version?: string | null;
status: string | null;
live: boolean | null;
type: string | null;
created_at?: string | null;
updated_at?: string | null;
started_at?: string | null;
ended_at?: string | null;
total_votes?: any;
snapshot?: App.DataTransferObjects.SnapshotData | null;
user: App.DataTransferObjects.UserData | null;
questions: Array<App.DataTransferObjects.QuestionData> | null;
voters?: Array<App.DataTransferObjects.QuestionData> | null;
responses?: any | null;
user_response?: App.DataTransferObjects.BallotResponseData | null;
votes?: Array<App.DataTransferObjects.QuestionData> | null;
tokens?: Array<App.DataTransferObjects.QuestionData> | null;
txs?: Array<App.DataTransferObjects.QuestionData> | null;
};
export type BallotResponseData = {
hash: string | null;
created_at?: string | null;
ballot: App.DataTransferObjects.BallotData | null;
question: App.DataTransferObjects.QuestionData | null;
choice: App.DataTransferObjects.QuestionChoiceData;
user: App.DataTransferObjects.UserData | null;
voting_power: App.DataTransferObjects.VotingPowerData | null;
};
export type QuestionChoiceData = {
hash: string | null;
title: string;
description?: string | null;
selected?: boolean | null;
created_at?: string | null;
question: App.DataTransferObjects.QuestionData | null;
ballot: App.DataTransferObjects.BallotData | null;
order: number;
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
choices_tally: Array<any> | null;
};
export type RegistrationData = {
hash: string;
power: number;
token?: App.DataTransferObjects.TokenData | null;
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
has_voting_powers: boolean;
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
name: string;
voter_id: string | null;
hero?: string | null;
ballots?: Array<any> | null;
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
