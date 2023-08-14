###################
# BUILD FOR LOCAL DEVELOPMENT
###################
FROM node:18-alpine As development

WORKDIR /usr/src/app

COPY --chown=node:node package.json ./
COPY --chown=node:node yarn.lock ./

RUN yarn

COPY --chown=node:node . .

USER node

###################
# BUILD FOR PRODUCTION
###################
FROM node:18-alpine As build

WORKDIR /usr/src/app

COPY --chown=node:node package.json ./
COPY --chown=node:node yarn.lock ./

COPY --chown=node:node --from=development /usr/src/app/node_modules ./node_modules

COPY --chown=node:node . .

RUN yarn build

ENV NODE_ENV production

RUN yarn install --frozen-lockfile --production && yarn cache clean

USER node


###################
# PRODUCTION
###################
FROM node:18-alpine As production

WORKDIR /usr/src/app

COPY --chown=node:node --from=build /usr/src/app/node_modules /usr/src/app/dist/node_modules
COPY --chown=node:node --from=build /usr/src/app/dist /usr/src/app/dist

COPY --chown=node:node package.json /usr/src/app/dist/
COPY --chown=node:node yarn.lock /usr/src/app/dist/

CMD [ "node", "dist/main.js" ]