<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Phrases</title>
</head>
<body>
  
  <form method="post" id="search-word" autocomplete="off">
    <input type="text" name="word">
    <button type="submit">send</button>
  </form>

  <form action="server.php?action=file" enctype="multipart/form-data" method="post">
    <input type="file" name="file">
    <button type="submit">send file</button>
  </form>


  <div class="output"></div>

  <script>
    const form = document.querySelector('#search-word');
    const output = document.querySelector('.output');
    const endpoint = 'https://www.wordreference.com/';

    form.addEventListener('submit', async function (e) {
      e.preventDefault();

      const word = this.word.value;
      
      const res = await search(`enpt/${word}`);

      output.innerHTML = res;

      const allowedClasses = ['odd', 'even'];

      const trs = Array.from(
        document.querySelectorAll('tr'))
          .filter(i => allowedClasses.some(a => i.classList.contains(a))
      );

      output.innerHTML = '';

      const regex = /\b(v|loc|express|expres|vt|vtr|expr|n|vi|prep|sf|lig|interj|aux|int|adv)\b|(⇒|\+)/gi;

      const items = [];

      trs.forEach(tr => {
        if(tr.id.startsWith('enpt')) {

          const frwrd = tr.querySelector('td.FrWrd').textContent.replace(regex, '').trim();

          items.push({ phrases: [], frwrd, meanings: [] });
        }

        const phrase = tr.querySelector('td.FrEx')?.textContent;

        if(phrase) {
          items[items.length - 1].phrases.push(phrase);
        }

        const towrd = tr.querySelector('td.ToWrd')?.textContent.replace(regex, '').trim();

        if(towrd) {
          items[items.length - 1].meanings.push(towrd);
        }

      });

      console.log(items);

      await post(items
      .filter(i => i.phrases.length >= 1)
      .map(item => {
        let phrase = item.phrases.join(" ");
        let meanings = item.meanings.join(" ");

        return [item.phrases, item.meanings, item.frwrd].join("\t");
      }), word);
    });

    async function search(uri) {
      const res = await fetch(endpoint + uri);
      return res.text();
    }

    async function post(data, word) {
      const res = await fetch('http://localhost/lang/server.php?action=add&word=' + word, {
        method: 'POST',
        body: JSON.stringify(data)
      })

      alert(await res.text());
    }
  </script>
</body>
</html>