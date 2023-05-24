import axios from 'axios';

export default class BlockfrostKeysService {

  constructor () {

  };

  public async getConfig() {
      try {
        return (await axios.get(`${window.location.origin}/api/cardano/config`))?.data;
      } catch (error) {
          throw error;
      }
  }


};