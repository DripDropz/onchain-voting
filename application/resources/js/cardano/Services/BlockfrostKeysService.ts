import axios from 'axios';

export default class BlockfrostKeysService {

  constructor () {

  };

  public async getConfig() {
      try {
        return (await axios.get(route('config.cardano')))?.data;
      } catch (error) {
          throw error;
      }
  }


};
