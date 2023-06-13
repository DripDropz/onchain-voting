import { Module } from '@nestjs/common';
import { AppController } from './app.controller.js';
import { AppService } from './app.service.js';
import {WalletModule} from "./modules/wallet/wallet.module.js";

@Module({
  imports: [WalletModule],
  controllers: [AppController],
  providers: [AppService],
})
export class AppModule {}
