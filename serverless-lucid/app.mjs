import express from 'express'
import serverless from 'serverless-http'
import registrationRouter from './routes/registration.mjs'
import voteRouter from './routes/vote.mjs'
import walletRouter from './routes/wallet.mjs'
import {checkHeader} from './lib/config.mjs'
import bodyParser from "body-parser";

const app = express()
app.use(bodyParser.json())

// Apply the "checkHeader" middleware
app.use(checkHeader)

// Error handler
app.use((err, req, res, next) => {
    res.status(400).send(err.message);
});

app.use(express.json());
app.use(express.urlencoded({extended: false}));
app.use('/registration', registrationRouter);
app.use('/vote', voteRouter);
app.use('/wallet', walletRouter);

const mode = process.env.MODE.trim()
const app_port = 80;

if (mode === 'dev') {
    console.log("Running in local development mode")
    app.listen(app_port, () => console.log(`Listening on: ${app_port}`));
}

export const handler = serverless(app);
