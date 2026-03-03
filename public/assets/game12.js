const enter = 'OK';
const del = 'Sup';
const numberOfLines = 7;
let gameOver = false;

// ===== Theme Toggle with localStorage =====
const themeToggle = document.getElementById('theme-toggle');
const themeIcon = document.getElementById('theme-icon');
const html = document.documentElement;

const applyTheme = (theme) => {
  if (theme === 'light') {
    html.setAttribute('data-theme', 'light');
    if (themeIcon) {
      themeIcon.classList.remove('fa-moon');
      themeIcon.classList.add('fa-sun');
    }
  } else {
    html.removeAttribute('data-theme');
    if (themeIcon) {
      themeIcon.classList.remove('fa-sun');
      themeIcon.classList.add('fa-moon');
    }
  }
};

const savedTheme = localStorage.getItem('wordleMonde-theme') || 'dark';
applyTheme(savedTheme);

if (themeToggle) {
  themeToggle.addEventListener('click', () => {
    const current = html.getAttribute('data-theme');
    const next = current === 'light' ? 'dark' : 'light';
    localStorage.setItem('wordleMonde-theme', next);
    applyTheme(next);
  });
}

// ===== Toast Notification =====
const toastEl = document.createElement('div');
toastEl.classList.add('toast-notification');
document.body.appendChild(toastEl);

const showToast = (message, duration = 1500) => {
  toastEl.textContent = message;
  toastEl.classList.add('show');
  setTimeout(() => {
    toastEl.classList.remove('show');
  }, duration);
};

// ===== Confetti CSS =====
const launchConfetti = () => {
  const container = document.createElement('div');
  container.classList.add('confetti-container');
  document.body.appendChild(container);

  const colors = ['#6aaa64', '#c9b458', '#538d4e', '#b59f3b', '#ffd700', '#ff6b6b', '#48dbfb', '#ff9ff3', '#54a0ff', '#5f27cd'];
  const count = 80;

  for (let i = 0; i < count; i++) {
    const piece = document.createElement('div');
    piece.classList.add('confetti-piece');
    const color = colors[Math.floor(Math.random() * colors.length)];
    const left = Math.random() * 100;
    const duration = 2 + Math.random() * 2;
    const delayVal = Math.random() * 1.5;
    const sway = (Math.random() - 0.5) * 200;

    piece.style.backgroundColor = color;
    piece.style.left = left + '%';
    piece.style.animationDuration = duration + 's';
    piece.style.animationDelay = delayVal + 's';
    piece.style.setProperty('--sway', sway + 'px');
    container.appendChild(piece);
  }

  setTimeout(() => {
    container.remove();
  }, 5000);
};
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
  // Supprimer le hint s'il existe
  const hint = square.parentElement.querySelector('.hint-letter');
  if (hint) {
    hint.remove();
  }
  inWorkingSquare++;
  square.innerHTML += letter;
  
  // Bordure plus marquee quand la case contient une lettre
  square.parentElement.classList.add('has-letter');
  
  // Ajouter l'animation pop
  square.parentElement.classList.add('bounce');
  setTimeout(() => {
    square.parentElement.classList.remove('bounce');
  }, 150);
};

const deleteLetterInSquare = () => {
  let square = document.getElementById(`square-${inWorkingLine}-${inWorkingSquare -1}`);
  if (square) {
    inWorkingSquare--;
    square.innerHTML = square.innerHTML.slice(0, -1);
    // Retirer la bordure marquee si la case est vide
    if (square.innerHTML.trim() === '') {
      square.parentElement.classList.remove('has-letter');
    }
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
      gameOver = true;
    } else {
      if (inWorkingLine === numberOfLines) {
        gameOver = true;
      }
    }
    // Flip 3D animation avec delai en cascade
    for (let i=0; i < data.result.length; i++) {
      const tile = currentLine.children[i];
      await delay(300);
      tile.classList.add('flip');
      // Appliquer la couleur a mi-flip (quand la tuile est invisible)
      setTimeout(() => {
        tile.classList.add(data.result[i]);
      }, 250);
    }
    // Attendre la fin de toutes les animations flip
    await delay(300);
    if (data.success) {
      currentLine.classList.add('tada');
      launchConfetti();
      displayVictory();
    } else if (inWorkingLine === numberOfLines) {
      displayDefeat();
    }
    // Afficher les lettres vertes sur la ligne suivante
    if (!gameOver) {
      displayGreenHints(data);
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
    currentLine.children[i].classList.remove('has-letter');
  }
  currentLine.classList.add('shake');
  showToast('Ce mot n\'existe pas dans Wikipedia !');
  setTimeout(() => {
    currentLine.classList.remove('shake');
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

// Fonction pour afficher les lettres vertes en transparence sur la ligne suivante
const displayGreenHints = (data) => {
  const nextLine = document.getElementById(`line-${inWorkingLine}`);
  if (!nextLine) return;
  
  // Récupérer TOUTES les lettres vertes trouvées jusqu'à présent (pas seulement la dernière ligne)
  const greenPositions = {};
  
  // Parcourir toutes les lignes précédentes pour accumuler les lettres vertes
  for (let lineNum = 1; lineNum < inWorkingLine; lineNum++) {
    const line = document.getElementById(`line-${lineNum}`);
    if (line) {
      for (let i = 0; i < line.children.length; i++) {
        const squareContainer = line.children[i];
        if (squareContainer.classList.contains('green')) {
          const letter = squareContainer.querySelector('.heart').textContent;
          greenPositions[i] = letter;
        }
      }
    }
  }
  
  // Afficher les hints sur la ligne suivante
  for (const [position, letter] of Object.entries(greenPositions)) {
    const squareContainer = nextLine.children[position];
    const square = squareContainer.querySelector('.heart');
    
    // Créer un élément hint seulement si la case est vide
    if (square && square.innerHTML.trim() === '') {
      const hint = document.createElement('span');
      hint.classList.add('hint-letter');
      hint.textContent = letter;
      squareContainer.appendChild(hint);
    }
  }
};

// Afficher les hints pour les lettres vertes déjà trouvées au chargement de la page
const displayInitialHints = () => {
  if (sessionColors[0] !== '' && !sessionSuccess && inWorkingLine < numberOfLines) {
    // Récupérer toutes les lettres vertes des lignes précédentes
    const greenPositions = {};
    
    for (let lineIndex = 0; lineIndex < sessionColors.length; lineIndex++) {
      const colors = sessionColors[lineIndex].split(',').map(c => c.trim());
      const lineElement = document.getElementById(`line-${lineIndex + 1}`);
      
      if (lineElement) {
        for (let i = 0; i < colors.length; i++) {
          if (colors[i] === 'green') {
            const letter = lineElement.children[i].querySelector('.heart').textContent;
            greenPositions[i] = letter;
          }
        }
      }
    }
    
    // Afficher les hints sur la ligne en cours
    const currentLine = document.getElementById(`line-${inWorkingLine}`);
    if (currentLine) {
      for (const [position, letter] of Object.entries(greenPositions)) {
        const squareContainer = currentLine.children[position];
        const square = squareContainer.querySelector('.heart');
        
        if (square && square.innerHTML.trim() === '') {
          const hint = document.createElement('span');
          hint.classList.add('hint-letter');
          hint.textContent = letter;
          squareContainer.appendChild(hint);
        }
      }
    }
  }
};

// Appeler la fonction au chargement
displayInitialHints();
