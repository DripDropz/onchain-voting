import { Module } from '@nestjs/common';
import {RegistrationController} from "./registration.controller.js";

@Module({
    controllers: [RegistrationController]
})
export class RegistrationModule {}
