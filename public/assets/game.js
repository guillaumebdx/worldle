const enter = 'OK';
const del = 'Sup';
let gameOver = false;
const letterCount = parseInt(document.getElementById('matrice').dataset.lettercount);
const keyboardLetters = document.getElementsByClassName('keyboard-letter');
const copyMe = document.getElementById('copy-me');
const copyButton = document.getElementById('copy-button');

let inWorkingLine = 1;
let inWorkingSquare = 1;

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

copyButton.addEventListener('click', () => {
  if (document.selection) {
    let range = document.body.createTextRange();
    range.moveToElementText(copyMe);
    range.select().createTextRange();
    document.execCommand("copy");
  } else if (window.getSelection) {
    let range = document.createRange();
    range.selectNode(copyMe);
    window.getSelection().addRange(range);
    document.execCommand("copy");
  }
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
    inWorkingLine++;
    inWorkingSquare = 1;
    fetch(`/check/${word}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json'
      },
    }).then(response => {
      return response.json();
    }).then(data => {
      colorize(data)
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

const colorize = (data) => {
  colorizeKeyboard(data)
  createCopyLine(data)
  let currentLine = document.getElementById(`line-${inWorkingLine -1}`);
  if (data.success) {
    currentLine.classList.add('tada');
    gameOver = true;
    displayVictory();
  } else {
    currentLine.classList.add('shake');
  }

  for (let i=0; i < data.result.length; i++) {
    currentLine.children[i].classList.add(data.result[i]);
  }
}

const displayVictory = () => {
  let victory = document.getElementById('victory');
  victory.style.display = 'block';
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
