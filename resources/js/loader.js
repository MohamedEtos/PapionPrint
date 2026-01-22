  /* Global Loader Injection */
  document.addEventListener("DOMContentLoaded", function () {
    // 1. Inject CSS
    var loaderCss = `
        #papion-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #ffffff;
            z-index: 99999;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            transition: opacity 0.5s ease-out;
        }
        #papion-loader.hidden {
            opacity: 0;
            pointer-events: none;
        }
        .loader-content {
            font-family: 'Alexandria', sans-serif;
            font-size: 24px;
            font-weight: 600;
            color: #7367f0; /* Vuexy Primary Color */
            display: flex;
            align-items: center;
        }
        .typewriter-text {
            overflow: hidden;
            border-right: .15em solid #7367f0;
            white-space: nowrap;
            margin: 0 auto;
            letter-spacing: .15em;
            animation: 
            typing 2.5s steps(30, end),
            blink-caret .75s step-end infinite;
        }
        
        @keyframes typing {
            from { width: 0 }
            to { width: 100% }
        }
        @keyframes blink-caret {
            from, to { border-color: transparent }
            50% { border-color: #7367f0; }
        }

    `;

    var styleCheck = document.getElementById('papion-loader-style');
    if (!styleCheck) {
      var style = document.createElement('style');
      style.id = 'papion-loader-style';
      style.type = 'text/css';
      style.appendChild(document.createTextNode(loaderCss));
      document.head.appendChild(style);
    }

    // 2. Inject HTML
    if (!document.getElementById('papion-loader')) {
      var loaderDiv = document.createElement('div');
      loaderDiv.id = 'papion-loader';
      loaderDiv.innerHTML = `
            <div class="loader-content">
                <span dir="ltr" class="typewriter-text"><b>Papion</b> System</span>
  
            </div>
        `;
      document.body.prepend(loaderDiv);
    }

    // 3. Hide on Load
    window.onload = function () {
      var loader = document.getElementById('papion-loader');
      if (loader) {
        loader.classList.add('hidden');
        setTimeout(function () {
          loader.remove(); // Clean up DOM
        }, 500); // 0.5s matches transition
      }
    };
  });
