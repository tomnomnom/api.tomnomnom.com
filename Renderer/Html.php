<?php
namespace Renderer;

class Html extends \Renderer {
  public function render($body){
    $this->renderHead();
    $this->renderFragment($body);
    $this->renderFooter();
  }

  protected function renderFragment($fragment){
    switch (strToLower(gettype($fragment))){
      case 'array':
      case 'stdclass':
        if ($this->isMap($fragment)){
          $this->renderMap($fragment);
        } else {
          $this->renderArray($fragment);
        }
        break;
      case 'string':
      default:
        $this->renderString($fragment);
        break;
    }
  }

  protected function isMap($candidate){
    $foundNonInt = false;
    foreach ($candidate as $key => $value){
      if (!is_int($key)){
        $foundNonInt = true;
        break;
      }
    }
    return $foundNonInt;
  }

  protected function renderMap($fragment){
    echo "<dl>\n";
    foreach ($fragment as $key => $value){
      echo "<dt>";
      $this->renderFragment($key);
      echo "</dt>\n";
      echo "<dd>";
      $this->renderFragment($value);
      echo "</dd>\n";
    }
    echo "</dl>\n";
  }

  protected function renderArray($fragment){
    echo "<ul>\n";
    foreach ($fragment as $key => $value){
      echo "<li>";
      $this->renderFragment($value);
      echo "</li>\n";
    }
    echo "</ul>\n";
  }

  protected function escape($string){
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
  }

  protected function renderString($string){
    if ($string[0] == '/'){
      echo '<a href="'.$this->escape($string).'">'.$this->escape($string).'</a>';
      return;
    }
    echo $this->escape($string);
  }

  protected function renderHead(){

    echo '<!doctype html>
      <html lang="en">
        <head>
          <title>api.tomnomnom.com</title>

          <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
          <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>

          <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
          <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Droid+Serif"/>

          <style>
            body {
              font-family: \'Droid Serif\', serif;
            }

            dl, ul {
                line-height: 1.5em;
            }

            dl { 
                margin: 0; 
            }
            dl dt { font-weight: bold; }
            dl dd { margin-left: 1em; margin-bottom: 0.5em; }
          </style>

          <script>
            $(function(){
              $("a").each(function(){
                var linkHtml = $(this).text();
                linkHtml = linkHtml.replace(
                  /:([a-zA-Z_\-]+)/g,
                  "<input type=\"text\" name=\"$1\" value=\"$1\"/>"
                );
                $(this).html(linkHtml);
              });
              $("a").click(function(event){
                event.preventDefault(); 
                var href = $(this).attr("href");
                var tokens = href.match(/:[a-zA-Z_\-]+/g);

                if (tokens){
                  for (var i = 0; i < tokens.length; i++){
                    var token = tokens[i]; 
                    var tokenName = token.replace(/:/, "");
                    var value = $(this).children("input[name="+tokenName+"]").val();
                    href = href.replace(new RegExp(token), value);
                  }
                }
                document.location = href;
              });
            });
          </script>

        </head>
        <body> 
    ';
  }

  protected function renderFooter(){
    echo '
      </body>
      </html>    
    ';
  }
}
