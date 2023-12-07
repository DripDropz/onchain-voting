import { Module } from '@nestjs/common';
import {WalletController} from "@chainvote/modules/wallet/wallet.controller.js";
import { AppConfigService } from '../../services/app-config.service.js';

@Module({
  controllers: [WalletController],
  providers: [AppConfigService],
})
export class WalletModule {}
