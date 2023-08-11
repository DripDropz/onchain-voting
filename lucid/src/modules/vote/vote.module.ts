import { Module } from '@nestjs/common';
import {VoteController} from "./vote.controller.js";

@Module({
    controllers: [VoteController]
})
export class VoteModule {}
