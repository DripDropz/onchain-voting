function compareValues(num1, num2, operator) {
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
