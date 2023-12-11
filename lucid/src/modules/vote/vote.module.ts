import { Module } from '@nestjs/common';
import {VoteController} from "./vote.controller.js";
import { AppConfigService } from '../../services/app-config.service.js';

@Module({
    controllers: [VoteController],
    providers: [AppConfigService],
})
export class VoteModule {}
