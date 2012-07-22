global $PLUGIN_DIR;
$cmd = $_POST['cmd'];

if ($cmd == "hljs_save")
{
    update_option('hljs_style', $_POST['hljs_style']);
    update_option('hljs_tab_replace', $_POST['hljs_tab_replace']);
    update_option('hljs_additional_css', $_POST['hljs_additional_css']);

    echo '<p class="info">' . __('All configurations successfully saved...', 'hljs') . '</p>';
}

?>

<!-- html code of settings page -->

<div class="wrap">

  <form id="hljs" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">

    <script type="text/javascript" src="<?php echo ($PLUGIN_DIR . '/' . 'highlight.pack.js'); ?>"></script>
    <script type="text/javascript">hljs.initHighlightingOnLoad();</script>
    <link rel="stylesheet" href="<?php echo ($PLUGIN_DIR . '/' . 'styles' . '/default.css'); ?>" />
  
    <style>
        .info { padding: 15px; background: #EDEDED; border: 1px solid #333333; font: 14px #333333 Verdana; margin: 30px 10px 0px 0px; }

        .section { padding: 10px; margin: 30px 0 0px; background: #FAFAFA; border: 1px solid #DDDDDD; display: block; }
        input[type="text"] { width: 400px; margin: 10px 0px 0px;}
        textarea {width: 400px; height: 100px; }

        #hljs_style { width: 200px;  margin: 10px 0px 0px;}
        #submit { min-width: 40px; margin-top: 20px; } 

        table.hljs_copyright { font-size: 8px; margin-top: 50px;}
        table.hljs_copyright tr {margin-bottom: 10px;}
        table.hljs_copyright tr td {padding: 5px; font: 12px Sans-Serif; border: 1px solid #DDDDDD;}

    </style>

    <!-- combo box with styles -->
    <p class="section">
      <label for="hljs_style"><?php echo __('Color Scheme', 'hljs') ?></label><br/>

      <select name="hljs_style" id="hljs_style">
         <?php hljs_get_style_list(get_option('hljs_style')); ?>
      </select>
    </p>

    <!-- text edit : tab replace -->
    <p class="section">
      <label for="hljs_tab_replace"><?php echo __('You can replaces TAB (\x09) characters used for indentation in your code with some fixed number of spaces or with a &lt;span&gt; to set them special styling', 'hljs') ?></label><br/>
      <input type="text" name="hljs_tab_replace" id="hljs_tab_replace" value="<?php echo get_option('hljs_tab_replace') ?>" />
    </p>

    <!-- text edit : additional css -->
    <p class="section">
      <label for="hljs_additional_css"><?php echo __('You can add some additional CSS rules for better display', 'hljs') ?></label><br/>
      <textarea type="text" name="hljs_additional_css" id="hljs_additional_css"><?php echo get_option('hljs_additional_css') ?></textarea>
    </p>

    <input type="hidden" name="cmd" value="hljs_save" />
    <input type="submit" name="submit" value="<?php echo __('Save', 'hljs') ?>" id="submit" />

  </form>

    <!-- copyright information -->
        <table border="0" class="hljs_copyright">
            <tr>
                <td width="120px" align="center"><?php echo __('Author', 'hljs'); ?></td>
                <td><p><a href="http://www.kalnitsky.org"><?php echo __('Igor Kalnitsky', 'hljs'); ?></a> &lt;<a href="mailto:igor@kalnitsky.org">igor@kalnitsky.org</a>&gt;</p></td>
            </tr>

            <tr>
                <td width="120px" align="center"><?php echo __('Plugin Info', 'hljs'); ?></td>
                <td><p><?php echo __('This is simple wordpress plugin for <a href="http://softwaremaniacs.org/soft/highlight/en/">highlight.js</a> library. <a href="http://softwaremaniacs.org/soft/highlight/en/">Highlight.js</a> highlights syntax in code examples on blogs, forums and in fact on any web pages. It&acute;s very easy to use because it works automatically: finds blocks of code, detects a language, highlights it.', 'hljs'); ?></p></td>
            </tr>

            <tr>
                <td width="120px" align="center"><?php echo __('Plugin Usage', 'hljs'); ?></td>
                <td><?php echo __('<p>For code highlighting you should use one of the following ways.</p>
                    
                    <p><strong>The first way</strong> is to use bb-codes:</p>
                    
                    <p><pre><code>[code] this language will be automatically determined [/code]</code></pre></p>
                    <p><pre><code>[code lang="cpp"] highlight the code with certain language [/code]</code></pre></p>
                    
                    <p><strong>The second way</strong> is to use html-tags:</p>
                    
                    <p><pre><code class="html">&lt;pre&gt;&lt;code&gt; this language will be automatically determined &lt;/code&gt;&lt;/pre&gt;</code></pre></p>
                    <p><pre><code class="html">&lt;pre&gt;&lt;code class="html"&gt; highlight the code with certain language &lt;/code&gt;&lt;/pre&gt;</code></pre></p>', 'hljs'); ?></td>
            </tr>

            <tr>
                <td width="120px" align="center"><?php echo __('Language Support', 'hljs'); ?></td>
                <td><p>
                    1C, ActionScript, Apache, AVR Asm, Axapta, Bash, CMake, CoffeeScript C++, C#,
                    CSS, D, Delphi, Diff, Django, Dos, Erlang, Erlang REPL, Go, GLSL, Haskell,
                    HTTP, Ini, Java, JavaScript, JSON, Lisp, Lua, Markdown, MatLab, MEL, Nginx,
                    Objective C, Parser3, Perl, PHP, Python profile, Python, R, RenderMan RSL,
                    RenderMan RIB, Ruby, Rust, Scala, Smalltalk, SQL, TeX, Vala, VBscript, VHDL,
                    HTML/XML, HTTP
                    </p>
                </td>
            </tr>

        </table>


</div>
