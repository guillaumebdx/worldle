const enter = 'OK';
const del = 'Sup';
const numberOfLines = 7;
let gameOver = false;
const matrice = document.getElementById('matrice');
const letterCount = parseInt(matrice.dataset.lettercount);
const reloadCount = parseInt(matrice.dataset.reloadcount);
const keyboardLetters = document.getElementsByClassName('keyboard-letter');
const copyMe = document.getElementById('copy-me');
const copyButton = document.getElementById('copy-button');
const sessionColors = matrice.dataset.colors.split('|');
const sessionSuccess = matrice.dataset.success;
const wordClient = document.getElementById('word-client').innerText;
let inWorkingLine = 1;
let inWorkingSquare = 1;
inWorkingLine += reloadCount;

for (let i = 0; i < keyboardLetters.length; i++) {
  keyboardLetters[i].addEventListener('click', function() {
    let letter = this.innerHTML;
    this.classList.add('pressed');
    if (letter !== enter && letter !== del && inWorkingSquare <= letterCount && !gameOver) {
      addLetterInSquare(letter);
    }
    if (letter === del) {
      deleteLetterInSquare();
    }
    if (letter === enter) {
      checkWord(getCurrentWord());
    }
    setTimeout(() => {
      this.classList.remove('pressed');
    }, 150);
  });
}

document.addEventListener('keydown', (event) => {
  if (event.key.length === 1) {
    //a z
    if (event.key.charCodeAt() >= 97 && event.key.charCodeAt() <=122 && inWorkingSquare <= letterCount && !gameOver) {
      addLetterInSquare(event.key.toUpperCase());
    }
    //A Z
    if (event.key.charCodeAt() >= 65 && event.key.charCodeAt() <=90 && inWorkingSquare <= letterCount && !gameOver) {
      addLetterInSquare(event.key.toUpperCase());
    }
  }

  if (event.key === 'Delete' && inWorkingSquare > 1 && !gameOver) {
    deleteLetterInSquare();
  }
  if (event.key === 'Backspace' && inWorkingSquare > 1 && !gameOver) {
    deleteLetterInSquare();
  }
  if (event.key === 'Enter' && !gameOver) {
    checkWord(getCurrentWord());
  }
});

copyButton.addEventListener('click', (event) => {
    event.stopPropagation();
    const lines = document.getElementsByClassName('line');
    let text = 'Mon @WordleMonde du jour #WordleMonde \n';
    let vip = document.getElementById('is-vip').innerText + '\n';
    text += vip;
    for (let i = 0; i < lines.length; i++) {
      for (square of lines[i].children) {
        text += square.innerHTML;
      }
      text += '\n';
    }
    text += 'https://wordlemonde.fr';
    navigator.clipboard.writeText(text);
    copyButton.innerHTML = 'Copié !';
});

const addLetterInSquare = (letter) => {
  let square = document.getElementById(`square-${inWorkingLine}-${inWorkingSquare}`);
  inWorkingSquare++;
  square.innerHTML += letter;
};

const deleteLetterInSquare = () => {
  let square = document.getElementById(`square-${inWorkingLine}-${inWorkingSquare -1}`);
  if (square) {
    inWorkingSquare--;
    square.innerHTML = square.innerHTML.slice(0, -1);
  }
};

const checkWord = (word) => {
  if (letterCount === inWorkingSquare -1) {
    inWorkingSquare = 1;
    fetch(`/check/${word}/${inWorkingLine}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json'
      },
    }).then(response => {
      return response.json();
    }).then(data => {
      if(data.wordServer === wordClient) {
        colorize(data)
      } else {
        window.location = '/error-word';
      }
    });
  } else {
    console.log('do not check word');
  }
}

const getCurrentWord = () => {
  let word = '';
  for (let i = 1; i < inWorkingSquare; i++) {
    let square = document.getElementById(`square-${inWorkingLine}-${i}`);
    word += square.innerHTML;
  }
  return word;
};

const colorize = async (data) => {
  let currentLine = document.getElementById(`line-${inWorkingLine}`);
  if (data.validWord === false) {
    handleInvalidWord(currentLine);
  } else {
    colorizeKeyboard(data)
    createCopyLine(data)
    inWorkingLine++;
    if (data.success) {
      currentLine.classList.add('tada');
      gameOver = true;
      displayVictory();
    } else {
      currentLine.classList.add('shake');
      if (inWorkingLine === numberOfLines) {
        gameOver = true;
        displayDefeat();
      }
    }
    for (let i=0; i < data.result.length; i++) {
      currentLine.children[i].classList.add(data.result[i]);
      if (data.result[i] === 'green') {
        pyro(currentLine.children[i]);
      }
      await delay(100)
    }
  }
}
const delay = ms => new Promise(res => setTimeout(res, ms));

const pyro = async (pyroBlock) => {
  pyroBlock.classList.add('pyro')
  await delay(1000)
  pyroBlock.classList.remove('pyro')
}
const handleInvalidWord = (currentLine) => {
  for (let i=0; i < currentLine.children.length; i++) {
    currentLine.children[i].children[0].innerHTML = '';
  }
  currentLine.classList.add('shake');
  const invalidWord = document.getElementById('invalid-word');
  invalidWord.classList.remove('d-none');
  setTimeout(() => {
    currentLine.classList.remove('shake');
    invalidWord.classList.add('d-none');
  }, 1000);
}

const displayVictory = () => {
  let victory = document.getElementById('victory');
  victory.style.display = 'block';
}

const displayDefeat = () => {
  let defeat = document.getElementById('defeat');
  defeat.style.display = 'block';
}

const colorizeKeyboard = (data) => {
  for (let i = 0; i < keyboardLetters.length; i++) {
    if (data.errors.includes(keyboardLetters[i].innerHTML)) {
      keyboardLetters[i].classList.add('blue');
    }
    if (data.valids.includes(keyboardLetters[i].innerHTML)) {
      keyboardLetters[i].classList.add('green');
    }
    if (data.aways.includes(keyboardLetters[i].innerHTML)) {
      keyboardLetters[i].classList.add('yellow');
    }
  }
}

const createCopyLine = (data) => {
  const newLine = document.createElement('div');
  newLine.classList.add('line');
  for (let i=0; i < data.result.length; i++) {
    let square = document.createElement('span');
    if (data.result[i] === 'green') {
      squareColor = '🟩';
    }
    if (data.result[i] === 'blue') {
      squareColor = '🟦';
    }
    if (data.result[i] === 'yellow') {
      squareColor = '🟨';
    }
    square.innerHTML = squareColor;
    newLine.appendChild(square);
  }
  copyMe.appendChild(newLine);
}

const createAllCopyLines = () => {
  for (let i = 0; i < sessionColors.length; i++) {
    let colors = sessionColors[i].split(',');
    for (let j=0; j <= colors.length; j++) {
      if (colors[j]) {
        colors[j] = colors[j].trim();
      }
    }
    createCopyLine({result : colors});
  }
}

if (sessionColors[0] !== '') {
  createAllCopyLines();
}

const sessionErrors = matrice.dataset.errors.split(',');
const sessionAways = matrice.dataset.aways.split(',');
const sessionValids = matrice.dataset.valids.split(',');
let sessionData = {
  errors: sessionErrors,
  aways: sessionAways,
  valids: sessionValids
};

colorizeKeyboard(sessionData);

if (sessionSuccess) {
  displayVictory();
}
