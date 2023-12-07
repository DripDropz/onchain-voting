import { Module } from '@nestjs/common';
import {RegistrationController} from "./registration.controller.js";
import { AppConfigService } from '../../services/app-config.service.js';

@Module({
    controllers: [RegistrationController],
    providers: [AppConfigService],
})
export class RegistrationModule {}
