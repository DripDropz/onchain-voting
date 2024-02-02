FROM node:20-alpine

WORKDIR /code

COPY package.json package.json
COPY package-lock.json package-lock.json

RUN npm install

COPY . .

EXPOSE 80

CMD [ "node", "app.mjs" ]
