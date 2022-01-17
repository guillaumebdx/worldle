const enter = 'OK';
const del = 'Sup';
let gameOver = false;
const letterCount = parseInt(document.getElementById('matrice').dataset.lettercount);
const keyboardLetters = document.getElementsByClassName('keyboard-letter');

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
  let currentLine = document.getElementById(`line-${inWorkingLine -1}`);
  console.log(data.success)
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
  console.log(currentLine);
}

const displayVictory = () => {
  let victory = document.getElementById('victory');
  victory.style.display = 'block';
}
