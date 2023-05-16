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
started_at?: string | null;
ended_at?: string | null;
total_votes?: any;
user: App.DataTransferObjects.UserData | null;
questions?: any;
voters?: Array<any> | null;
votes?: any;
tokens?: any;
txs?: any;
};
export type QuestionChoiceData = {
hash: string | null;
title: string;
description?: string | null;
created_at?: number | null;
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
status: string;
type: string;
user: App.DataTransferObjects.UserData | null;
ballot: App.DataTransferObjects.BallotData | null;
choices: Array<any> | null;
};
export type RegistrationData = {
hash: string;
power: number;
token?: App.DataTransferObjects.TokenData | null;
};
export type SnapshotData = {
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
hash: string;
stake_key: string;
vote_power?: string | null;
votes?: Array<any> | null;
registrations?: Array<any> | null;
tokens?: Array<any> | null;
txs?: Array<any> | null;
};
}
