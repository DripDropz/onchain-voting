/**
 * 
 * @param num1 
 * @param num2 
 * @param operator 
 * @returns boolean
 */
function compareValues(num1, num2, operator):boolean {
    switch (operator) {
        case ">=":
            return num1 >= num2;
            break;
        case ">":
            return num1 > num2;
            break;
        case '<=':
            return num1 <= num2;
            break;
        case '<':
            return num1 <= num2;
            break;
        case '=':
            return num1 = num2;
            break;
    }
}

export default compareValues;
