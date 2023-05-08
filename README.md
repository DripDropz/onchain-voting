# DripDropz Open Source On-Chain Voting #

## Description ##

The goal of this code is to provide information, a framework, and ultimately a full-stack solution to users and 
organizations seeking to conduct governance or voting on the Cardano blockchain.

## Rationale ##

Key features that have informed design decisions of this platform include, but are not limited, to the following:

- **Public Auditability**: It is the belief of our team that all pertinent and relevant information concerning a vote to be
  cast on-chain should be fully transparent and publicly auditable to all users (not withstanding some technical prowess)
  at any time past or present without reliance on 3rd parties.
- **Vote Security**: Particularly when conducting a vote utilizing on-chain assets (native $ADA or other native assets) the
  paramount importance is proof of voter participation intent. This is not to be confused with voter collusion which
  shall be addressed separately based upon historical observations.
- **Ease of Use**: The voting platform should be as easy to use, understand, and participate with as possible to 
  maximize voter participation in any particular vote or governance matter.

## Documentation ##

Please refer to the schema and usage documentation found under [docs](docs/README.md).

## License ##

[<img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by/4.0/88x31.png" />](http://creativecommons.org/licenses/by/4.0/)

**DripDropz On-Chain Voting** by the _DripDropz Team_ is licensed under a 
[Creative Commons Attribution 4.0 International License](http://creativecommons.org/licenses/by/4.0/).

All of the work, documentation, source code, and information contained within this repository is licensed under the 
Creative Commons 4.0 International "Attribution" license. Essentially this means you are free to use (including monetization),
modify, adapt, and redistribute the code in any way you see fit. The only thing we ask is that you provide attribution on any
derivative works or implementations. You may attribute either this repository or [DripDropz Team](https://dripdropz.io).
Please see [LICENSE](LICENSE.md) for the full details of the license.

## Other Ecosystem Developers and Participants ##

- **DripDropz**: This platform was built out of the work performed on behalf of DripDropz to conduct on-chain governance
  voting utilizing the $DRIP native asset. We have conducted several successful votes utilizing the techniques and
  solutions described in this repository. It has always been our goal to give back to the Cardano community by open
  sourcing solutions to common problems.
  - More information and historic vote results available at: [https://dripdropz.io/vote/](https://dripdropz.io/vote/)
- **SundaeSwap**: The team at SundaeSwap has been hard at work on their own version of on-chain governance using Merkle Tree
  roll-ups to the blockchain to provide vote auditability.
  - More information available at: [https://services.sundaeswap.finance/](https://services.sundaeswap.finance/)
- **Voteaire**: The team at Voteaire has created a simple and easy to use voting platform that is open and available today to
  all ecosystem participants. This platform was built out as an expansion to the earlier work performed by members of 
  the DripDropz Team, the Voteaire Team, and other community participants on behalf of SPOCRA.
  - Platform Available at: [https://voteaire.io](https://voteaire.io).
  - Github: [https://github.com/voteaire/voteaire-onchain-spec/](https://github.com/voteaire/voteaire-onchain-spec/)
- **SPOCRA**: The Stake Pool Operators Collective Representation Assemble (SPOCRA) conducted the very first on-chain voting
  on Cardano in late 2020. The status of the group itself is unknown at the time of writing but much of the early work
  done by the community and members of the DripDropz team to support and conduct the first SPOCRA votes has helped to 
  shape and grow our current platform.
  - Github: [https://github.com/SPOCRA/onchain-voting](https://github.com/SPOCRA/onchain-voting)
- We are happy to add any other community-driven projects developing on-chain voting for the Cardano ecosystem. Please
  let us know about them in the GitHub Issues tab above, so we can add them!

# Running Locally with docker-compose
### Pre-requisites
* Make sure you have docker installed and running. 
* Make sure you have make installed.


### Get up and running
1) Clone this repository: `git clone https://github.com/DripDropz/onchain-voting.git`    
2) cd into the project directory: `cd onchain-voting`   
3) Run `cp ./application/.env.example ./application/.env`
4) Run `make init` to install all frontend and backend dependencies and start the docker services.
5) Run `make watch` to start the vite dev server and watch for changes.
6) Navigate to `http://localhost:8080` in your browser.         


# Makefile Commands
* [dev](#dev)

* [watch](#watch)

* [docker-setup](#docker-setup)

* [backend-setup](#backend-setup)

* [backend-install](#backend-install)

* [frontend-install](#frontend-install)

* [frontend-clean](#frontend-clean)

* [rm](#rm)
 
* [down](#down)

* [up](#up)
* 
* [test](#test)

## dev
`make dev`  
Runs docker services in the background, 
installs composer dependencies, and generates application key.

## watch
`make watch`  
Starts vite dev server and watches for changes.

## docker-setup
`make docker-setup`  
Runs docker services in the background.

## backend-setup
`make backend-setup`  
Installs laravel composer dependencies,
and generates application key.

## backend-install
`make backend-install`  
Installs laravel composer dependencies.

## frontend-install
`make frontend-install`  
Delete and reinstall node_modules.

## frontend-clean
`make frontend-clean`  
Delete node_modules, lock files and yarn cache.

## rm
`make rm`  
remove all docker containers and volumes.

## down
`make down`  
shutdown all docker containers but keep volumes.

## up
`make up`  
start docker containers.


## test-backend
`make test-backend`  
Run pest php tests.

