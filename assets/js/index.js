const subtitlesForm = document.querySelector('form.subtitles');
const translateForm = document.querySelector('form.translate');
const toggleControl = document.querySelector('div.toggle');
const text = document.querySelector('div.text');
const output = document.querySelector('div.output');

let activeTranslateForm = true;
let pre;
let pos;

toggleControl.addEventListener('click', function() {
  activeTranslateForm = !activeTranslateForm;

  this.classList.toggle('off');

  translateForm.style.display = 'none';
})

window.addEventListener('keydown', function(e) {
  if(e.key === 'Escape') {
    toggleControl.click();
  }
})

window.addEventListener('load', async function() {
  const txt = await get('load');

  text.textContent = txt;
  output.innerHTML = toTag(txt);
})

subtitlesForm.addEventListener('change', async function() {
  const form = new FormData();
  form.append('subtitles', this.subtitles.files[0]);

  const res = await post(form, 'upload');

  text.textContent = res;
});

translateForm.addEventListener('submit', async function(e) {
  e.preventDefault();

  const word = this.word.value;
  const means = this.means.value;

  const txt = pre + `[${means}:${word}]` + pos;

  text.textContent = txt;
  output.innerHTML = toTag(txt);

  const res = await post(text.textContent, 'save');

  console.log(res);

  this.style.display = 'none';

  this.word.value = null;
  this.means.value = null;
})

text.addEventListener('mouseup', function(e) {
  const curX = e.clientX - this.offsetLeft;
  const curY = e.clientY - document.querySelector('main.container').offsetTop + window.scrollY;
  const selection = window.getSelection();

  const txt = this.textContent;

  pre = txt.substring(0, selection.anchorOffset);
  pos = txt.substring(selection.focusOffset, txt.length);

  if(activeTranslateForm) {
    translateForm.style.top = curY + 'px';
    translateForm.style.left = curX + 'px';
    translateForm.style.display = 'block';

    translateForm.word.value = txt.substring(selection.anchorOffset, selection.focusOffset);
    translateForm.means.focus();
  }
})

async function post(data, act) {
  const url = window.location.href + '/server/index.php?act=';
  const res = await fetch(url + act, {
    method: 'POST',
    body: data
  });

  return res.text();
}

async function get(act, param = '') {
  const url = window.location.href + '/server/index.php?act=' + act;
  const res = await fetch(url + param);

  return res.text();
}

function toTag(text) {
  return text.replace(/\[(.*?):(.*?)\]/g, '<span class="word"><span>$1</span>$2</span>');
}