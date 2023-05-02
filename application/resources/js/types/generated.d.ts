declare namespace App.DataTransferObjects {
export type BallotData = {
hash: string;
title: string;
description?: string | null;
version?: string | null;
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
hash: string;
stakeKey: string;
votePower?: string | null;
votes?: Array<any> | null;
registrations?: Array<any> | null;
tokens?: Array<any> | null;
txs?: Array<any> | null;
};
}
