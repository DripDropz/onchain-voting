import { Module } from '@nestjs/common';
import { AppController } from './app.controller.js';
import { AppService } from './app.service.js';
import { WalletModule } from './modules/wallet/wallet.module.js';
import { VoteModule } from './modules/vote/vote.module.js';
import { RegistrationModule } from './modules/registration/registration.module.js';
import { ConfigModule } from '@nestjs/config';

@Module({
  imports: [
    ConfigModule.forRoot({
      envFilePath: `${process.cwd()}/.env`,
      cache: true,
      isGlobal: true,
    }),
    RegistrationModule,
    VoteModule,
    WalletModule
  ],
  controllers: [AppController],
  providers: [AppService],
})
export class AppModule {}
