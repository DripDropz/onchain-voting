declare namespace App.DataTransferObjects {
export type BallotData = {
hash: string;
title: string;
description?: string | null;
version?: string | null;
<<<<<<< Updated upstream
=======
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
questions?: Array<App.DataTransferObjects.QuestionData> | null;
voters?: Array<App.DataTransferObjects.QuestionData> | null;
votes?: Array<App.DataTransferObjects.QuestionData> | null;
tokens?: Array<App.DataTransferObjects.QuestionData> | null;
txs?: Array<App.DataTransferObjects.QuestionData> | null;
};
export type BallotResponseData = {
hash: string | null;
created_at?: string | null;
user: App.DataTransferObjects.UserData;
ballot?: App.DataTransferObjects.BallotData | null;
question?: App.DataTransferObjects.QuestionData | null;
choice?: App.DataTransferObjects.QuestionChoiceData | null;
voting_power?: App.DataTransferObjects.VotingPowerData | null;
};
export type QuestionChoiceData = {
hash: string | null;
title: string;
description?: string | null;
selected?: boolean | null;
created_at?: string | null;
question: App.DataTransferObjects.QuestionData | null;
ballot: App.DataTransferObjects.BallotData | null;
};
export type QuestionData = {
hash: string | null;
title: string;
description?: string | null;
supplemental?: string | null;
max_choices?: number | null;
created_at?: string | null;
>>>>>>> Stashed changes
status: string;
type: string;
totalVotes?: any;
user: App.DataTransferObjects.UserData;
questions?: Array<any> | null;
voters?: Array<any> | null;
votes?: Array<any> | null;
tokens?: Array<any> | null;
txs?: Array<any> | null;
};
export type QuestionChoicesData = {
hash: string;
question: App.DataTransferObjects.QuestionData;
};
export type QuestionData = {
hash: string;
choices: Array<any>;
};
export type RegistrationData = {
hash: string;
power: number;
token?: App.DataTransferObjects.TokenData | null;
};
export type SnapshotData = {
<<<<<<< Updated upstream
=======
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
>>>>>>> Stashed changes
};
export type TokenData = {
hash: string;
policyId: string;
ballot?: App.DataTransferObjects.BallotData | null;
voter: App.DataTransferObjects.VoterData;
};
export type TxData = {
};
export type UserData = {
hash: string;
name: string;
hero?: string | null;
ballots?: Array<any>;
};
export type VoteData = {
hash: string;
power: number | null;
token: App.DataTransferObjects.TokenData | null;
voter: App.DataTransferObjects.VoterData;
ballot: App.DataTransferObjects.BallotData | null;
};
export type VoterData = {
<<<<<<< Updated upstream
hash: string;
stakeKey: string;
votePower?: string | null;
votes?: Array<any> | null;
registrations?: Array<any> | null;
tokens?: Array<any> | null;
txs?: Array<any> | null;
=======
voter_id: string;
votes?: App.DataTransferObjects.VoteData | null;
registrations?: App.DataTransferObjects.RegistrationData | null;
tokens?: App.DataTransferObjects.TokenData | null;
txs?: App.DataTransferObjects.TxData | null;
>>>>>>> Stashed changes
};
}
