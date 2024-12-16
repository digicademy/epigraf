<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Error: You are not authorized to access that location.    </title>
    <link href="/favicon.ico" type="image/x-icon" rel="icon"/><link href="/favicon.ico" type="image/x-icon" rel="shortcut icon"/>    <style>
    * {
        box-sizing: border-box;
    }
    body {
        font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
        color: #404041;
        background: #F5F7FA;
        font-size: 14px;
        letter-spacing: .01em;
        line-height: 1.6;
        padding: 0 0 40px;
        margin: 0;
        height: 100%;
    }
    header {
        flex: 1;
        background-color: #D33C47;
        color: #ffffff;
        padding: 10px;
    }
    .header-title {
        display: flex;
        align-items: center;
        font-size: 30px;
        margin: 0;
    }
    .header-title a {
        font-size: 18px;
        cursor: pointer;
        margin-left: 10px;
        user-select: none;
    }
    .header-title code {
        margin: 0 10px;
    }
    .header-description {
        display: block;
        font-size: 18px;
        line-height: 1.2;
        margin-bottom: 16px;
    }
    .header-type {
        display: block;
        font-size: 16px;
    }
    .header-help a {
        color: #fff;
    }

    .error-content {
        display: flex;
    }
    .col-left,
    .col-right {
        overflow-y: auto;
        padding: 10px;
    }
    .col-left {
        background: #ececec;
        flex: 0 0 30%;
    }
    .col-right {
        flex: 1;
    }

    .toggle-vendor-frames {
        color: #404041;
        display: block;
        padding: 5px;
        margin-bottom: 10px;
        text-align: center;
        text-decoration: none;
    }
    .toggle-vendor-frames:hover,
    .toggle-vendor-frames:active {
        background: #e5e5e5;
    }

    .code-dump,
    pre {
        background: #fff;
        border-radius: 4px;
        padding: 5px;
        white-space: pre-wrap;
        margin: 0;
    }

    .error,
    .error-subheading {
        font-size: 18px;
        margin-top: 0;
        padding: 20px 16px;
    }
    .error-subheading {
        color: #fff;
        background-color: #319795;
    }
    .error-subheading strong {
        color: #fff;
        background-color: #4fd1c5;
        border-radius: 9999px;
        padding: 4px 12px;
        margin-right: 8px;
    }
    .error {
        color: #fff;
        background: #2779BD;
    }
    .error strong {
        color: #fff;
        background-color: #6CB2EB;
        border-radius: 9999px;
        padding: 4px 12px;
        margin-right: 8px;
    }

    .stack-trace {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .stack-frame {
        background: #e5e5e5;
        padding: 10px;
        margin-bottom: 10px;
    }
    .stack-frame:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    .stack-frame a {
        display: block;
        color: #212121;
        text-decoration: none;
    }
    .stack-frame.active {
        background: #F5F7FA;
    }
    .stack-frame a:hover {
        text-decoration: underline;
    }
    .stack-frame-header {
        display: flex;
        align-items: center;
    }
    .stack-frame-file a {
        color: #212121;
    }

    .stack-frame-args {
        flex: 0 0 150px;
        display: block;
        padding: 8px 14px;
        text-decoration: none;
        background-color: #606c76;
        border-radius: 4px;
        cursor: pointer;
        color: #fff;
        text-align: center;
        margin-bottom: 10px;
    }
    .stack-frame-args:hover {
        background-color: #D33C47;
    }

    .stack-frame-file {
        flex: 1;
        word-break:break-all;
        margin-right: 10px;
        font-size: 16px;
    }
    .stack-file,
    .stack-function {
        display: block;
    }

    .stack-frame-file,
    .stack-file {
        font-family: consolas, monospace;
    }
    .stack-function {
        font-weight: bold;
    }
    .stack-file {
        font-size: 0.9em;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
        direction: rtl;
    }

    .stack-details {
        background: #ececec;
        border-radius: 4px;
        padding: 10px;
        margin-bottom: 18px;
    }

    .code-excerpt {
        width: 100%;
        margin: 10px 0 0 0;
        background: #fefefe;
    }
    .code-highlight {
        display: block;
        background: #fff59d;
    }
    .excerpt-line {
        padding: 0;
    }
    .excerpt-number {
        background: #f6f6f6;
        width: 50px;
        text-align: right;
        color: #666;
        border-right: 1px solid #ddd;
        padding: 2px;
    }
    .excerpt-number:after {
        content: attr(data-number);
    }
    .cake-debug {
        margin-top: 10px;
    }

    table {
        text-align: left;
    }
    th, td {
        padding: 4px;
    }
    th {
        border-bottom: 1px solid #ccc;
    }
    </style>
</head>
<body>
    <header>
                <h1 class="header-title">
            <span>You are not authorized to access that location.</span>
            <a>&#128203</a>
        </h1>
                <span class="header-type">Cake\Http\Exception\ForbiddenException</span>
    </header>
    <div class="error-content">
        <div class="col-left">
            <a href="#" class="toggle-link toggle-vendor-frames">Toggle Vendor Stack Frames</a>

<ul class="stack-trace">
        <li class="stack-frame vendor-frame active">
        <a href="#" data-target="stack-frame-0">
                        <span class="stack-file">
                            CORE/src/Controller/Component/AuthComponent.php:422                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-1">
                            <span class="stack-function">Cake\Controller\Component\AuthComponent-&gt;_unauthorized</span>
                        <span class="stack-file">
                            CORE/src/Controller/Component/AuthComponent.php:305                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-2">
                            <span class="stack-function">Cake\Controller\Component\AuthComponent-&gt;authCheck</span>
                        <span class="stack-file">
                            CORE/src/Event/EventManager.php:309                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-3">
                            <span class="stack-function">Cake\Event\EventManager-&gt;_callListener</span>
                        <span class="stack-file">
                            CORE/src/Event/EventManager.php:286                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-4">
                            <span class="stack-function">Cake\Event\EventManager-&gt;dispatch</span>
                        <span class="stack-file">
                            CORE/src/Event/EventDispatcherTrait.php:92                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-5">
                            <span class="stack-function">Cake\Controller\Controller-&gt;dispatchEvent</span>
                        <span class="stack-file">
                            CORE/src/Controller/Controller.php:573                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-6">
                            <span class="stack-function">Cake\Controller\Controller-&gt;startupProcess</span>
                        <span class="stack-file">
                            CORE/src/Controller/ControllerFactory.php:72                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-7">
                            <span class="stack-function">Cake\Controller\ControllerFactory-&gt;invoke</span>
                        <span class="stack-file">
                            CORE/src/Http/BaseApplication.php:251                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-8">
                            <span class="stack-function">Cake\Http\BaseApplication-&gt;handle</span>
                        <span class="stack-file">
                            CORE/src/Http/Runner.php:77                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-9">
                            <span class="stack-function">Cake\Http\Runner-&gt;handle</span>
                        <span class="stack-file">
                            CORE/src/Http/Middleware/BodyParserMiddleware.php:159                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-10">
                            <span class="stack-function">Cake\Http\Middleware\BodyParserMiddleware-&gt;process</span>
                        <span class="stack-file">
                            CORE/src/Http/Runner.php:73                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-11">
                            <span class="stack-function">Cake\Http\Runner-&gt;handle</span>
                        <span class="stack-file">
                            CORE/src/Routing/Middleware/RoutingMiddleware.php:166                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-12">
                            <span class="stack-function">Cake\Routing\Middleware\RoutingMiddleware-&gt;process</span>
                        <span class="stack-file">
                            CORE/src/Http/Runner.php:73                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-13">
                            <span class="stack-function">Cake\Http\Runner-&gt;handle</span>
                        <span class="stack-file">
                            CORE/src/Http/Middleware/HttpsEnforcerMiddleware.php:81                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-14">
                            <span class="stack-function">Cake\Http\Middleware\HttpsEnforcerMiddleware-&gt;process</span>
                        <span class="stack-file">
                            CORE/src/Http/Runner.php:73                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-15">
                            <span class="stack-function">Cake\Http\Runner-&gt;handle</span>
                        <span class="stack-file">
                            CORE/src/Routing/Middleware/AssetMiddleware.php:68                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-16">
                            <span class="stack-function">Cake\Routing\Middleware\AssetMiddleware-&gt;process</span>
                        <span class="stack-file">
                            CORE/src/Http/Runner.php:73                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-17">
                            <span class="stack-function">Cake\Http\Runner-&gt;handle</span>
                        <span class="stack-file">
                            CORE/src/Error/Middleware/ErrorHandlerMiddleware.php:121                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-18">
                            <span class="stack-function">Cake\Error\Middleware\ErrorHandlerMiddleware-&gt;process</span>
                        <span class="stack-file">
                            CORE/src/Http/Runner.php:73                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-19">
                            <span class="stack-function">Cake\Http\Runner-&gt;handle</span>
                        <span class="stack-file">
                            CORE/src/Http/Runner.php:58                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-20">
                            <span class="stack-function">Cake\Http\Runner-&gt;run</span>
                        <span class="stack-file">
                            CORE/src/Http/Server.php:90                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-21">
                            <span class="stack-function">Cake\Http\Server-&gt;run</span>
                        <span class="stack-file">
                            CORE/src/TestSuite/MiddlewareDispatcher.php:190                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-22">
                            <span class="stack-function">Cake\TestSuite\MiddlewareDispatcher-&gt;execute</span>
                        <span class="stack-file">
                            CORE/src/TestSuite/IntegrationTestTrait.php:499                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-23">
                            <span class="stack-function">App\Test\TestCase\AppTestCase-&gt;_sendRequest</span>
                        <span class="stack-file">
                            CORE/src/TestSuite/IntegrationTestTrait.php:385                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-24">
                            <span class="stack-function">App\Test\TestCase\AppTestCase-&gt;get</span>
                        <span class="stack-file">
                            ROOT/tests/TestCase/Controller/WikiControllerTest.php:93                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-25">
                            <span class="stack-function">App\Test\TestCase\Controller\WikiControllerTest-&gt;testNotAuthorizedReader</span>
                        <span class="stack-file">
                            ROOT/vendor/phpunit/phpunit/src/Framework/TestCase.php:1471                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-26">
                            <span class="stack-function">PHPUnit\Framework\TestCase-&gt;runTest</span>
                        <span class="stack-file">
                            ROOT/vendor/phpunit/phpunit/src/Framework/TestCase.php:1091                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-27">
                            <span class="stack-function">PHPUnit\Framework\TestCase-&gt;runBare</span>
                        <span class="stack-file">
                            ROOT/vendor/phpunit/phpunit/src/Framework/TestResult.php:703                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-28">
                            <span class="stack-function">PHPUnit\Framework\TestResult-&gt;run</span>
                        <span class="stack-file">
                            ROOT/vendor/phpunit/phpunit/src/Framework/TestCase.php:819                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-29">
                            <span class="stack-function">PHPUnit\Framework\TestCase-&gt;run</span>
                        <span class="stack-file">
                            ROOT/vendor/phpunit/phpunit/src/Framework/TestSuite.php:627                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-30">
                            <span class="stack-function">PHPUnit\Framework\TestSuite-&gt;run</span>
                        <span class="stack-file">
                            ROOT/vendor/phpunit/phpunit/src/Framework/TestSuite.php:627                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-31">
                            <span class="stack-function">PHPUnit\Framework\TestSuite-&gt;run</span>
                        <span class="stack-file">
                            ROOT/vendor/phpunit/phpunit/src/Framework/TestSuite.php:627                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-32">
                            <span class="stack-function">PHPUnit\Framework\TestSuite-&gt;run</span>
                        <span class="stack-file">
                            ROOT/vendor/phpunit/phpunit/src/TextUI/TestRunner.php:656                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-33">
                            <span class="stack-function">PHPUnit\TextUI\TestRunner-&gt;doRun</span>
                        <span class="stack-file">
                            ROOT/vendor/phpunit/phpunit/src/TextUI/Command.php:236                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-34">
                            <span class="stack-function">PHPUnit\TextUI\Command-&gt;run</span>
                        <span class="stack-file">
                            ROOT/vendor/phpunit/phpunit/src/TextUI/Command.php:195                        </span>
        </a>
    </li>
        <li class="stack-frame vendor-frame">
        <a href="#" data-target="stack-frame-35">
                            <span class="stack-function">PHPUnit\TextUI\Command::main</span>
                        <span class="stack-file">
                            ROOT/vendor/phpunit/phpunit/phpunit:61                        </span>
        </a>
    </li>
</ul>
        </div>
        <div class="col-right">

                <div id="stack-frame-0" style="display:block;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/Controller/Component/AuthComponent.php&amp;line=422">CORE/src/Controller/Component/AuthComponent.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-0">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="418"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">*/</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="419"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">protected&nbsp;function&nbsp;</span><span style="color: #0000BB">_unauthorized</span><span style="color: #007700">(</span><span style="color: #0000BB">Controller&nbsp;$controller</span><span style="color: #007700">):&nbsp;?</span><span style="color: #0000BB">Response</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="420"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="421"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">_config</span><span style="color: #007700">[</span><span style="color: #DD0000">'unauthorizedRedirect'</span><span style="color: #007700">]&nbsp;===&nbsp;</span><span style="color: #0000BB">false</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="422"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">throw&nbsp;new&nbsp;</span><span style="color: #0000BB">ForbiddenException</span><span style="color: #007700">(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">_config</span><span style="color: #007700">[</span><span style="color: #DD0000">'authError'</span><span style="color: #007700">]);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="423"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="424"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="425"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">flash</span><span style="color: #007700">(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">_config</span><span style="color: #007700">[</span><span style="color: #DD0000">'authError'</span><span style="color: #007700">]);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="426"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">_config</span><span style="color: #007700">[</span><span style="color: #DD0000">'unauthorizedRedirect'</span><span style="color: #007700">]&nbsp;===&nbsp;</span><span style="color: #0000BB">true</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-0" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                    </div>
    </div>
    <div id="stack-frame-1" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/Controller/Component/AuthComponent.php&amp;line=305">CORE/src/Controller/Component/AuthComponent.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-1">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="301"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="302"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="303"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$event</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">stopPropagation</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="304"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="305"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">_unauthorized</span><span style="color: #007700">(</span><span style="color: #0000BB">$controller</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="306"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="307"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="308"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #FF8000">/**</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="309"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">*&nbsp;</span><span style="color: #0000BB">Events&nbsp;supported&nbsp;by&nbsp;this&nbsp;component</span><span style="color: #007700">.</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-1" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mApp\Controller\WikiController[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;39mauthorized[0m[0;90m => [0m[0;90m[[0m
    [0;32m'token'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m[0;90m,[0m
    [0;32m'reader'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m[0;90m,[0m
    [0;32m'author'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'*'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'editor'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'*'[0m
    [0;90m][0m
  [0;90m][0m
  [0;39mpaginate[0m[0;90m => [0m[0;90m[[0m
    [0;32m'limit'[0m[0;90m => [0m[0;35m(int)[0m [1;34m25[0m[0;90m,[0m
    [0;32m'order'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'Wiki.category'[0m[0;90m => [0m[0;32m'asc'[0m[0;90m,[0m
      [0;32m'Wiki.sortkey'[0m[0;90m => [0m[0;32m'asc'[0m[0;90m,[0m
      [0;32m'Wiki.name'[0m[0;90m => [0m[0;32m'asc'[0m
    [0;90m][0m
  [0;90m][0m
  [0;39mmenu[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;39msidemenu[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;39mallowedDatabases[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;39mactiveDatabase[0m[0;90m => [0m[1;33mfalse[0m
  [0;39maccessMode[0m[0;90m => [0m[0;32m'reader'[0m
  [0;39mSecurity[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\Component\SecurityComponent[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m
    [0;39m'components'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
    [0;39m'implementedEvents'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'Controller.startup'[0m[0;90m => [0m[0;32m'startup'[0m
    [0;90m][0m
    [0;39m'_config'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'blackHoleCallback'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'requireSecure'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'unlockedFields'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'unlockedActions'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'validatePost'[0m[0;90m => [0m[1;33mfalse[0m
    [0;90m][0m
    [0;39mcomponents[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_defaultConfig[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_action[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_registry[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ComponentRegistry[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_componentMap[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_config[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_configInitialized[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m
  [0;39mRequestHandler[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\Component\RequestHandlerComponent[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m
    [0;39m'components'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
    [0;39m'implementedEvents'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'Controller.startup'[0m[0;90m => [0m[0;32m'startup'[0m[0;90m,[0m
      [0;32m'Controller.beforeRender'[0m[0;90m => [0m[0;32m'beforeRender'[0m
    [0;90m][0m
    [0;39m'_config'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'checkHttpCache'[0m[0;90m => [0m[1;33mtrue[0m[0;90m,[0m
      [0;32m'viewClassMap'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'enableBeforeRedirect'[0m[0;90m => [0m[1;33mfalse[0m
    [0;90m][0m
    [0;39mcomponents[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mext[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_renderType[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_defaultConfig[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_registry[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ComponentRegistry[0m[0;90m) id:[0m[1;34m2[0m[0;90m {}[0m
    [0;35mprotected[0m [0;39m_componentMap[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_config[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_configInitialized[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m
  [0;39mFlash[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\Component\FlashComponent[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m
    [0;39m'components'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
    [0;39m'implementedEvents'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
    [0;39m'_config'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'key'[0m[0;90m => [0m[0;32m'flash'[0m[0;90m,[0m
      [0;32m'element'[0m[0;90m => [0m[0;32m'default'[0m[0;90m,[0m
      [0;32m'params'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'clear'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
      [0;32m'duplicate'[0m[0;90m => [0m[1;33mfalse[0m
    [0;90m][0m
    [0;39mcomponents[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_defaultConfig[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_registry[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ComponentRegistry[0m[0;90m) id:[0m[1;34m2[0m[0;90m {}[0m
    [0;35mprotected[0m [0;39m_componentMap[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_config[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_configInitialized[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m
  [0;39mApiPagination[0m[0;90m => [0m[0;90mobject([0m[0;36mBryanCrowe\ApiPagination\Controller\Component\ApiPaginationComponent[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m
    [0;39m'components'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
    [0;39m'implementedEvents'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'Controller.beforeRender'[0m[0;90m => [0m[0;32m'beforeRender'[0m
    [0;90m][0m
    [0;39m'_config'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'key'[0m[0;90m => [0m[0;32m'pagination'[0m[0;90m,[0m
      [0;32m'aliases'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'visible'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m
    [0;90m][0m
    [0;39mcomponents[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_defaultConfig[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mpagingInfo[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_registry[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ComponentRegistry[0m[0;90m) id:[0m[1;34m2[0m[0;90m {}[0m
    [0;35mprotected[0m [0;39m_componentMap[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_config[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_configInitialized[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m
  [0;39mAuth[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\Component\AuthComponent[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m
    [0;39m'components'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'RequestHandler'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'Flash'[0m
    [0;90m][0m
    [0;39m'implementedEvents'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'Controller.initialize'[0m[0;90m => [0m[0;32m'authCheck'[0m[0;90m,[0m
      [0;32m'Controller.startup'[0m[0;90m => [0m[0;32m'startup'[0m
    [0;90m][0m
    [0;39m'_config'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'authenticate'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'authorize'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'flash'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'loginAction'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'loginRedirect'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'logoutRedirect'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'authError'[0m[0;90m => [0m[0;32m'You are not authorized to access that location.'[0m[0;90m,[0m
      [0;32m'unauthorizedRedirect'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
      [0;32m'storage'[0m[0;90m => [0m[0;32m'Session'[0m[0;90m,[0m
      [0;32m'checkAuthIn'[0m[0;90m => [0m[0;32m'Controller.initialize'[0m
    [0;90m][0m
    [0;39mcomponents[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;39mallowedActions[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_defaultConfig[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_authenticateObjects[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_authorizeObjects[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_storage[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Auth\Storage\SessionStorage[0m[0;90m) id:[0m[1;34m7[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_authenticationProvider[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_authorizationProvider[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_registry[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ComponentRegistry[0m[0;90m) id:[0m[1;34m2[0m[0;90m {}[0m
    [0;35mprotected[0m [0;39m_componentMap[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_config[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_configInitialized[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_eventManager[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Event\EventManager[0m[0;90m) id:[0m[1;34m8[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_eventClass[0m[0;90m => [0m[0;32m'Cake\Event\Event'[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mname[0m[0;90m => [0m[0;32m'Wiki'[0m
  [0;35mprotected[0m [0;39mrequest[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\ServerRequest[0m[0;90m) id:[0m[1;34m9[0m[0;90m {[0m
    [0;39mtrustProxy[0m[0;90m => [0m[1;33mfalse[0m
    [0;35mprotected[0m [0;39mparams[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mdata[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mquery[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mcookies[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_environment[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mbase[0m[0;90m => [0m[0;32m''[0m
    [0;35mprotected[0m [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
    [0;35mprotected[0m [0;39mtrustedProxies[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_detectors[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_detectorCache[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\PhpInputStream[0m[0;90m) id:[0m[1;34m10[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39muri[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\Uri[0m[0;90m) id:[0m[1;34m11[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39msession[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session[0m[0;90m) id:[0m[1;34m12[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39mattributes[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39memulatedAttributes[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39muploadedFiles[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mprotocol[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39mrequestTarget[0m[0;90m => [0m[1;33mnull[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mresponse[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Response[0m[0;90m) id:[0m[1;34m13[0m[0;90m {[0m
    [0;39m'status'[0m[0;90m => [0m[0;35m(int)[0m [1;34m200[0m
    [0;39m'contentType'[0m[0;90m => [0m[0;32m'text/html'[0m
    [0;39m'headers'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'Content-Type'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m
    [0;90m][0m
    [0;39m'file'[0m[0;90m => [0m[1;33mnull[0m
    [0;39m'fileRange'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
    [0;39m'cookies'[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Cookie\CookieCollection[0m[0;90m) id:[0m[1;34m14[0m[0;90m {[0m[0;90m}[0m
    [0;39m'cacheDirectives'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
    [0;39m'body'[0m[0;90m => [0m[0;32m''[0m
    [0;35mprotected[0m [0;39m_statusCodes[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_mimeTypes[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_status[0m[0;90m => [0m[0;35m(int)[0m [1;34m200[0m
    [0;35mprotected[0m [0;39m_file[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_fileRange[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_charset[0m[0;90m => [0m[0;32m'UTF-8'[0m
    [0;35mprotected[0m [0;39m_cacheDirectives[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_cookies[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Cookie\CookieCollection[0m[0;90m) id:[0m[1;34m14[0m[0;90m {}[0m
    [0;35mprotected[0m [0;39m_reasonPhrase[0m[0;90m => [0m[0;32m'OK'[0m
    [0;35mprotected[0m [0;39m_streamMode[0m[0;90m => [0m[0;32m'wb+'[0m
    [0;35mprotected[0m [0;39m_streamTarget[0m[0;90m => [0m[0;32m'php://memory'[0m
    [0;35mprotected[0m [0;39mheaders[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mheaderNames[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprivate[0m [0;39mprotocol[0m[0;90m => [0m[0;32m'1.1'[0m
    [0;35mprivate[0m [0;39mstream[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\Stream[0m[0;90m) id:[0m[1;34m15[0m[0;90m {[0m[0;90m}[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39m_responseClass[0m[0;90m => [0m[0;32m'Cake\Http\Response'[0m
  [0;35mprotected[0m [0;39mautoRender[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprotected[0m [0;39m_components[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ComponentRegistry[0m[0;90m) id:[0m[1;34m2[0m[0;90m {}[0m
  [0;35mprotected[0m [0;39mplugin[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39m_eventManager[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Event\EventManager[0m[0;90m) id:[0m[1;34m8[0m[0;90m {}[0m
  [0;35mprotected[0m [0;39m_eventClass[0m[0;90m => [0m[0;32m'Cake\Event\Event'[0m
  [0;35mprotected[0m [0;39m_tableLocator[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\ORM\Locator\TableLocator[0m[0;90m) id:[0m[1;34m16[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mlocations[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_config[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39minstances[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_fallbacked[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39moptions[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mmodelClass[0m[0;90m => [0m[0;32m'Wiki'[0m
  [0;35mprotected[0m [0;39m_modelFactories[0m[0;90m => [0m[0;90m[[0m
    [0;32m'Table'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\ORM\Locator\TableLocator[0m[0;90m) id:[0m[1;34m16[0m[0;90m {}[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'get'[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_modelType[0m[0;90m => [0m[0;32m'Table'[0m
  [0;35mprotected[0m [0;39m_viewBuilder[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\View\ViewBuilder[0m[0;90m) id:[0m[1;34m17[0m[0;90m {[0m
    [0;35mprotected[0m [0;39m_templatePath[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_template[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_plugin[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_theme[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_layout[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_autoLayout[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_layoutPath[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_name[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_className[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_options[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_helpers[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_vars[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
  [0;90m}[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-2" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/Event/EventManager.php&amp;line=309">CORE/src/Event/EventManager.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-2">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="305"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">protected&nbsp;function&nbsp;</span><span style="color: #0000BB">_callListener</span><span style="color: #007700">(callable&nbsp;</span><span style="color: #0000BB">$listener</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">EventInterface&nbsp;$event</span><span style="color: #007700">)</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="306"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="307"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$data&nbsp;</span><span style="color: #007700">=&nbsp;(array)</span><span style="color: #0000BB">$event</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">getData</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="308"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="309"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$listener</span><span style="color: #007700">(</span><span style="color: #0000BB">$event</span><span style="color: #007700">,&nbsp;...</span><span style="color: #0000BB">array_values</span><span style="color: #007700">(</span><span style="color: #0000BB">$data</span><span style="color: #007700">));</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="310"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="311"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="312"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #FF8000">/**</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="313"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">*&nbsp;@</span><span style="color: #0000BB">inheritDoc</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-2" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Event\Event[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;35mprotected[0m [0;39m_name[0m[0;90m => [0m[0;32m'Controller.initialize'[0m
  [0;35mprotected[0m [0;39m_subject[0m[0;90m => [0m[0;90mobject([0m[0;36mApp\Controller\WikiController[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m
    [0;39mauthorized[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;39mpaginate[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;39mmenu[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;39msidemenu[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;39mallowedDatabases[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;39mactiveDatabase[0m[0;90m => [0m[1;33mfalse[0m
    [0;39maccessMode[0m[0;90m => [0m[0;32m'reader'[0m
    [0;39mSecurity[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\Component\SecurityComponent[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m
    [0;39mRequestHandler[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\Component\RequestHandlerComponent[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m[0;90m}[0m
    [0;39mFlash[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\Component\FlashComponent[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m[0;90m}[0m
    [0;39mApiPagination[0m[0;90m => [0m[0;90mobject([0m[0;36mBryanCrowe\ApiPagination\Controller\Component\ApiPaginationComponent[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m[0;90m}[0m
    [0;39mAuth[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\Component\AuthComponent[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39mname[0m[0;90m => [0m[0;32m'Wiki'[0m
    [0;35mprotected[0m [0;39mrequest[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\ServerRequest[0m[0;90m) id:[0m[1;34m7[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39mresponse[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Response[0m[0;90m) id:[0m[1;34m8[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_responseClass[0m[0;90m => [0m[0;32m'Cake\Http\Response'[0m
    [0;35mprotected[0m [0;39mautoRender[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_components[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ComponentRegistry[0m[0;90m) id:[0m[1;34m9[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39mplugin[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_eventManager[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Event\EventManager[0m[0;90m) id:[0m[1;34m10[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_eventClass[0m[0;90m => [0m[0;32m'Cake\Event\Event'[0m
    [0;35mprotected[0m [0;39m_tableLocator[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\ORM\Locator\TableLocator[0m[0;90m) id:[0m[1;34m11[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39mmodelClass[0m[0;90m => [0m[0;32m'Wiki'[0m
    [0;35mprotected[0m [0;39m_modelFactories[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_modelType[0m[0;90m => [0m[0;32m'Table'[0m
    [0;35mprotected[0m [0;39m_viewBuilder[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\View\ViewBuilder[0m[0;90m) id:[0m[1;34m12[0m[0;90m {[0m[0;90m}[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39m_data[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mresult[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39m_stopped[0m[0;90m => [0m[1;33mtrue[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-3" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/Event/EventManager.php&amp;line=286">CORE/src/Event/EventManager.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-3">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="282"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">foreach&nbsp;(</span><span style="color: #0000BB">$listeners&nbsp;</span><span style="color: #007700">as&nbsp;</span><span style="color: #0000BB">$listener</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="283"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$event</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">isStopped</span><span style="color: #007700">())&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="284"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">break;</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="285"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="286"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$result&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">_callListener</span><span style="color: #007700">(</span><span style="color: #0000BB">$listener</span><span style="color: #007700">[</span><span style="color: #DD0000">'callable'</span><span style="color: #007700">],&nbsp;</span><span style="color: #0000BB">$event</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="287"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$result&nbsp;</span><span style="color: #007700">===&nbsp;</span><span style="color: #0000BB">false</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="288"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$event</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">stopPropagation</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="289"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="290"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$result&nbsp;</span><span style="color: #007700">!==&nbsp;</span><span style="color: #0000BB">null</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-3" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90m[[0m
  [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\Component\AuthComponent[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
    [0;39m'components'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'RequestHandler'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'Flash'[0m
    [0;90m][0m
    [0;39m'implementedEvents'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'Controller.initialize'[0m[0;90m => [0m[0;32m'authCheck'[0m[0;90m,[0m
      [0;32m'Controller.startup'[0m[0;90m => [0m[0;32m'startup'[0m
    [0;90m][0m
    [0;39m'_config'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'authenticate'[0m[0;90m => [0m[0;90m[[0m
        [0;32m'Form'[0m[0;90m => [0m[0;90m[[0m
          [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
        [0;90m][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'authorize'[0m[0;90m => [0m[0;90m[[0m
        [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'Controller'[0m
      [0;90m][0m[0;90m,[0m
      [0;32m'flash'[0m[0;90m => [0m[0;90m[[0m
        [0;32m'element'[0m[0;90m => [0m[0;32m'error'[0m[0;90m,[0m
        [0;32m'key'[0m[0;90m => [0m[0;32m'flash'[0m[0;90m,[0m
        [0;32m'params'[0m[0;90m => [0m[0;90m[[0m
          [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
        [0;90m][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'loginAction'[0m[0;90m => [0m[0;90m[[0m
        [0;32m'controller'[0m[0;90m => [0m[0;32m'Users'[0m[0;90m,[0m
        [0;32m'action'[0m[0;90m => [0m[0;32m'login'[0m[0;90m,[0m
        [0;32m'plugin'[0m[0;90m => [0m[1;33mnull[0m
      [0;90m][0m[0;90m,[0m
      [0;32m'loginRedirect'[0m[0;90m => [0m[0;90m[[0m
        [0;32m'controller'[0m[0;90m => [0m[0;32m'Users'[0m[0;90m,[0m
        [0;32m'action'[0m[0;90m => [0m[0;32m'start'[0m
      [0;90m][0m[0;90m,[0m
      [0;32m'logoutRedirect'[0m[0;90m => [0m[0;90m[[0m
        [0;32m'controller'[0m[0;90m => [0m[0;32m'Docs'[0m[0;90m,[0m
        [0;32m'action'[0m[0;90m => [0m[0;32m'display'[0m[0;90m,[0m
        [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'start'[0m
      [0;90m][0m[0;90m,[0m
      [0;32m'authError'[0m[0;90m => [0m[0;32m'You are not authorized to access that location.'[0m[0;90m,[0m
      [0;32m'unauthorizedRedirect'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
      [0;32m'storage'[0m[0;90m => [0m[0;32m'Session'[0m[0;90m,[0m
      [0;32m'checkAuthIn'[0m[0;90m => [0m[0;32m'Controller.initialize'[0m
    [0;90m][0m
    [0;39mcomponents[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'RequestHandler'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'Flash'[0m
    [0;90m][0m
    [0;39mallowedActions[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
    [0;35mprotected[0m [0;39m_defaultConfig[0m[0;90m => [0m[0;90m[[0m
      [0;32m'authenticate'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'authorize'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'flash'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'loginAction'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'loginRedirect'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'logoutRedirect'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'authError'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'unauthorizedRedirect'[0m[0;90m => [0m[1;33mtrue[0m[0;90m,[0m
      [0;32m'storage'[0m[0;90m => [0m[0;32m'Session'[0m[0;90m,[0m
      [0;32m'checkAuthIn'[0m[0;90m => [0m[0;32m'Controller.startup'[0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_authenticateObjects[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
    [0;35mprotected[0m [0;39m_authorizeObjects[0m[0;90m => [0m[0;90m[[0m
      [0;32m'Controller'[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Auth\ControllerAuthorize[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_storage[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Auth\Storage\SessionStorage[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_authenticationProvider[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_authorizationProvider[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_registry[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ComponentRegistry[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_componentMap[0m[0;90m => [0m[0;90m[[0m
      [0;32m'RequestHandler'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'Flash'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_config[0m[0;90m => [0m[0;90m[[0m
      [0;32m'authenticate'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'authorize'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'flash'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'loginAction'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'loginRedirect'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'logoutRedirect'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'authError'[0m[0;90m => [0m[0;32m'You are not authorized to access that location.'[0m[0;90m,[0m
      [0;32m'unauthorizedRedirect'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
      [0;32m'storage'[0m[0;90m => [0m[0;32m'Session'[0m[0;90m,[0m
      [0;32m'checkAuthIn'[0m[0;90m => [0m[0;32m'Controller.initialize'[0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_configInitialized[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_eventManager[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Event\EventManager[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_eventClass[0m[0;90m => [0m[0;32m'Cake\Event\Event'[0m
  [0;90m}[0m[0;90m,[0m
  [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'authCheck'[0m
[0;90m][0m</div>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Event\Event[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;35mprotected[0m [0;39m_name[0m[0;90m => [0m[0;32m'Controller.initialize'[0m
  [0;35mprotected[0m [0;39m_subject[0m[0;90m => [0m[0;90mobject([0m[0;36mApp\Controller\WikiController[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m
    [0;39mauthorized[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;39mpaginate[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;39mmenu[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;39msidemenu[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;39mallowedDatabases[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;39mactiveDatabase[0m[0;90m => [0m[1;33mfalse[0m
    [0;39maccessMode[0m[0;90m => [0m[0;32m'reader'[0m
    [0;39mSecurity[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\Component\SecurityComponent[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m
    [0;39mRequestHandler[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\Component\RequestHandlerComponent[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m[0;90m}[0m
    [0;39mFlash[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\Component\FlashComponent[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m[0;90m}[0m
    [0;39mApiPagination[0m[0;90m => [0m[0;90mobject([0m[0;36mBryanCrowe\ApiPagination\Controller\Component\ApiPaginationComponent[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m[0;90m}[0m
    [0;39mAuth[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\Component\AuthComponent[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39mname[0m[0;90m => [0m[0;32m'Wiki'[0m
    [0;35mprotected[0m [0;39mrequest[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\ServerRequest[0m[0;90m) id:[0m[1;34m7[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39mresponse[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Response[0m[0;90m) id:[0m[1;34m8[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_responseClass[0m[0;90m => [0m[0;32m'Cake\Http\Response'[0m
    [0;35mprotected[0m [0;39mautoRender[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_components[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ComponentRegistry[0m[0;90m) id:[0m[1;34m9[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39mplugin[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_eventManager[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Event\EventManager[0m[0;90m) id:[0m[1;34m10[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_eventClass[0m[0;90m => [0m[0;32m'Cake\Event\Event'[0m
    [0;35mprotected[0m [0;39m_tableLocator[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\ORM\Locator\TableLocator[0m[0;90m) id:[0m[1;34m11[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39mmodelClass[0m[0;90m => [0m[0;32m'Wiki'[0m
    [0;35mprotected[0m [0;39m_modelFactories[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_modelType[0m[0;90m => [0m[0;32m'Table'[0m
    [0;35mprotected[0m [0;39m_viewBuilder[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\View\ViewBuilder[0m[0;90m) id:[0m[1;34m12[0m[0;90m {[0m[0;90m}[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39m_data[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mresult[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39m_stopped[0m[0;90m => [0m[1;33mtrue[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-4" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/Event/EventDispatcherTrait.php&amp;line=92">CORE/src/Event/EventDispatcherTrait.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-4">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="88"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="89"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="90"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #FF8000">/**&nbsp;@var&nbsp;\Cake\Event\EventInterface&nbsp;$event&nbsp;*/</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="91"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$event&nbsp;</span><span style="color: #007700">=&nbsp;new&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">_eventClass</span><span style="color: #007700">(</span><span style="color: #0000BB">$name</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">$subject</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">$data</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="92"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">getEventManager</span><span style="color: #007700">()-&gt;</span><span style="color: #0000BB">dispatch</span><span style="color: #007700">(</span><span style="color: #0000BB">$event</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="93"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="94"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$event</span><span style="color: #007700">;</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="95"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="96"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span><span style="color: #007700">}</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-4" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Event\Event[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;35mprotected[0m [0;39m_name[0m[0;90m => [0m[0;32m'Controller.initialize'[0m
  [0;35mprotected[0m [0;39m_subject[0m[0;90m => [0m[0;90mobject([0m[0;36mApp\Controller\WikiController[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m
    [0;39mauthorized[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;39mpaginate[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;39mmenu[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;39msidemenu[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;39mallowedDatabases[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;39mactiveDatabase[0m[0;90m => [0m[1;33mfalse[0m
    [0;39maccessMode[0m[0;90m => [0m[0;32m'reader'[0m
    [0;39mSecurity[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\Component\SecurityComponent[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m
    [0;39mRequestHandler[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\Component\RequestHandlerComponent[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m[0;90m}[0m
    [0;39mFlash[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\Component\FlashComponent[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m[0;90m}[0m
    [0;39mApiPagination[0m[0;90m => [0m[0;90mobject([0m[0;36mBryanCrowe\ApiPagination\Controller\Component\ApiPaginationComponent[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m[0;90m}[0m
    [0;39mAuth[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\Component\AuthComponent[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39mname[0m[0;90m => [0m[0;32m'Wiki'[0m
    [0;35mprotected[0m [0;39mrequest[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\ServerRequest[0m[0;90m) id:[0m[1;34m7[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39mresponse[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Response[0m[0;90m) id:[0m[1;34m8[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_responseClass[0m[0;90m => [0m[0;32m'Cake\Http\Response'[0m
    [0;35mprotected[0m [0;39mautoRender[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_components[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ComponentRegistry[0m[0;90m) id:[0m[1;34m9[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39mplugin[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_eventManager[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Event\EventManager[0m[0;90m) id:[0m[1;34m10[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_eventClass[0m[0;90m => [0m[0;32m'Cake\Event\Event'[0m
    [0;35mprotected[0m [0;39m_tableLocator[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\ORM\Locator\TableLocator[0m[0;90m) id:[0m[1;34m11[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39mmodelClass[0m[0;90m => [0m[0;32m'Wiki'[0m
    [0;35mprotected[0m [0;39m_modelFactories[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_modelType[0m[0;90m => [0m[0;32m'Table'[0m
    [0;35mprotected[0m [0;39m_viewBuilder[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\View\ViewBuilder[0m[0;90m) id:[0m[1;34m12[0m[0;90m {[0m[0;90m}[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39m_data[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mresult[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39m_stopped[0m[0;90m => [0m[1;33mtrue[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-5" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/Controller/Controller.php&amp;line=573">CORE/src/Controller/Controller.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-5">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="569"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">*&nbsp;@return&nbsp;\</span><span style="color: #0000BB">Psr</span><span style="color: #007700">\</span><span style="color: #0000BB">Http</span><span style="color: #007700">\</span><span style="color: #0000BB">Message</span><span style="color: #007700">\</span><span style="color: #0000BB">ResponseInterface</span><span style="color: #007700">|</span><span style="color: #0000BB">null</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="570"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">*/</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="571"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">public&nbsp;function&nbsp;</span><span style="color: #0000BB">startupProcess</span><span style="color: #007700">():&nbsp;?</span><span style="color: #0000BB">ResponseInterface</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="572"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="573"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$event&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">dispatchEvent</span><span style="color: #007700">(</span><span style="color: #DD0000">'Controller.initialize'</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="574"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$event</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">getResult</span><span style="color: #007700">()&nbsp;instanceof&nbsp;</span><span style="color: #0000BB">ResponseInterface</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="575"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$event</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">getResult</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="576"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="577"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$event&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">dispatchEvent</span><span style="color: #007700">(</span><span style="color: #DD0000">'Controller.startup'</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-5" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;32m'Controller.initialize'[0m</div>
                    </div>
    </div>
    <div id="stack-frame-6" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/Controller/ControllerFactory.php&amp;line=72">CORE/src/Controller/ControllerFactory.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-6">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="68"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">*&nbsp;@</span><span style="color: #0000BB">psalm</span><span style="color: #007700">-</span><span style="color: #0000BB">param&nbsp;</span><span style="color: #007700">\</span><span style="color: #0000BB">Cake</span><span style="color: #007700">\</span><span style="color: #0000BB">Controller</span><span style="color: #007700">\</span><span style="color: #0000BB">Controller&nbsp;$controller</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="69"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">*/</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="70"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">public&nbsp;function&nbsp;</span><span style="color: #0000BB">invoke</span><span style="color: #007700">(</span><span style="color: #0000BB">$controller</span><span style="color: #007700">):&nbsp;</span><span style="color: #0000BB">ResponseInterface</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="71"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="72"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$result&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$controller</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">startupProcess</span><span style="color: #007700">();</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="73"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$result&nbsp;</span><span style="color: #007700">instanceof&nbsp;</span><span style="color: #0000BB">ResponseInterface</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="74"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$result</span><span style="color: #007700">;</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="75"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="76"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-6" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">No arguments</div>
                    </div>
    </div>
    <div id="stack-frame-7" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/Http/BaseApplication.php&amp;line=251">CORE/src/Http/BaseApplication.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-7">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="247"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="248"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="249"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$controller&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">controllerFactory</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">create</span><span style="color: #007700">(</span><span style="color: #0000BB">$request</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="250"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="251"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">controllerFactory</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">invoke</span><span style="color: #007700">(</span><span style="color: #0000BB">$controller</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="252"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="253"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="254"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-7" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mApp\Controller\WikiController[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;39mauthorized[0m[0;90m => [0m[0;90m[[0m
    [0;32m'token'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m[0;90m,[0m
    [0;32m'reader'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m[0;90m,[0m
    [0;32m'author'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'*'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'editor'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'*'[0m
    [0;90m][0m
  [0;90m][0m
  [0;39mpaginate[0m[0;90m => [0m[0;90m[[0m
    [0;32m'limit'[0m[0;90m => [0m[0;35m(int)[0m [1;34m25[0m[0;90m,[0m
    [0;32m'order'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'Wiki.category'[0m[0;90m => [0m[0;32m'asc'[0m[0;90m,[0m
      [0;32m'Wiki.sortkey'[0m[0;90m => [0m[0;32m'asc'[0m[0;90m,[0m
      [0;32m'Wiki.name'[0m[0;90m => [0m[0;32m'asc'[0m
    [0;90m][0m
  [0;90m][0m
  [0;39mmenu[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;39msidemenu[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;39mallowedDatabases[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;39mactiveDatabase[0m[0;90m => [0m[1;33mfalse[0m
  [0;39maccessMode[0m[0;90m => [0m[0;32m'reader'[0m
  [0;39mSecurity[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\Component\SecurityComponent[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m
    [0;39m'components'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
    [0;39m'implementedEvents'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'Controller.startup'[0m[0;90m => [0m[0;32m'startup'[0m
    [0;90m][0m
    [0;39m'_config'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'blackHoleCallback'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'requireSecure'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'unlockedFields'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'unlockedActions'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'validatePost'[0m[0;90m => [0m[1;33mfalse[0m
    [0;90m][0m
    [0;39mcomponents[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_defaultConfig[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_action[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_registry[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ComponentRegistry[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_componentMap[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_config[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_configInitialized[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m
  [0;39mRequestHandler[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\Component\RequestHandlerComponent[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m
    [0;39m'components'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
    [0;39m'implementedEvents'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'Controller.startup'[0m[0;90m => [0m[0;32m'startup'[0m[0;90m,[0m
      [0;32m'Controller.beforeRender'[0m[0;90m => [0m[0;32m'beforeRender'[0m
    [0;90m][0m
    [0;39m'_config'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'checkHttpCache'[0m[0;90m => [0m[1;33mtrue[0m[0;90m,[0m
      [0;32m'viewClassMap'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'enableBeforeRedirect'[0m[0;90m => [0m[1;33mfalse[0m
    [0;90m][0m
    [0;39mcomponents[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mext[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_renderType[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_defaultConfig[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_registry[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ComponentRegistry[0m[0;90m) id:[0m[1;34m2[0m[0;90m {}[0m
    [0;35mprotected[0m [0;39m_componentMap[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_config[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_configInitialized[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m
  [0;39mFlash[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\Component\FlashComponent[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m
    [0;39m'components'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
    [0;39m'implementedEvents'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
    [0;39m'_config'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'key'[0m[0;90m => [0m[0;32m'flash'[0m[0;90m,[0m
      [0;32m'element'[0m[0;90m => [0m[0;32m'default'[0m[0;90m,[0m
      [0;32m'params'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'clear'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
      [0;32m'duplicate'[0m[0;90m => [0m[1;33mfalse[0m
    [0;90m][0m
    [0;39mcomponents[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_defaultConfig[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_registry[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ComponentRegistry[0m[0;90m) id:[0m[1;34m2[0m[0;90m {}[0m
    [0;35mprotected[0m [0;39m_componentMap[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_config[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_configInitialized[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m
  [0;39mApiPagination[0m[0;90m => [0m[0;90mobject([0m[0;36mBryanCrowe\ApiPagination\Controller\Component\ApiPaginationComponent[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m
    [0;39m'components'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
    [0;39m'implementedEvents'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'Controller.beforeRender'[0m[0;90m => [0m[0;32m'beforeRender'[0m
    [0;90m][0m
    [0;39m'_config'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'key'[0m[0;90m => [0m[0;32m'pagination'[0m[0;90m,[0m
      [0;32m'aliases'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'visible'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m
    [0;90m][0m
    [0;39mcomponents[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_defaultConfig[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mpagingInfo[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_registry[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ComponentRegistry[0m[0;90m) id:[0m[1;34m2[0m[0;90m {}[0m
    [0;35mprotected[0m [0;39m_componentMap[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_config[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_configInitialized[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m
  [0;39mAuth[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\Component\AuthComponent[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m
    [0;39m'components'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'RequestHandler'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'Flash'[0m
    [0;90m][0m
    [0;39m'implementedEvents'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'Controller.initialize'[0m[0;90m => [0m[0;32m'authCheck'[0m[0;90m,[0m
      [0;32m'Controller.startup'[0m[0;90m => [0m[0;32m'startup'[0m
    [0;90m][0m
    [0;39m'_config'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'authenticate'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'authorize'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'flash'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'loginAction'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'loginRedirect'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'logoutRedirect'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'authError'[0m[0;90m => [0m[0;32m'You are not authorized to access that location.'[0m[0;90m,[0m
      [0;32m'unauthorizedRedirect'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
      [0;32m'storage'[0m[0;90m => [0m[0;32m'Session'[0m[0;90m,[0m
      [0;32m'checkAuthIn'[0m[0;90m => [0m[0;32m'Controller.initialize'[0m
    [0;90m][0m
    [0;39mcomponents[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;39mallowedActions[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_defaultConfig[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_authenticateObjects[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_authorizeObjects[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_storage[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Auth\Storage\SessionStorage[0m[0;90m) id:[0m[1;34m7[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_authenticationProvider[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_authorizationProvider[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_registry[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ComponentRegistry[0m[0;90m) id:[0m[1;34m2[0m[0;90m {}[0m
    [0;35mprotected[0m [0;39m_componentMap[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_config[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_configInitialized[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_eventManager[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Event\EventManager[0m[0;90m) id:[0m[1;34m8[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_eventClass[0m[0;90m => [0m[0;32m'Cake\Event\Event'[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mname[0m[0;90m => [0m[0;32m'Wiki'[0m
  [0;35mprotected[0m [0;39mrequest[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\ServerRequest[0m[0;90m) id:[0m[1;34m9[0m[0;90m {[0m
    [0;39mtrustProxy[0m[0;90m => [0m[1;33mfalse[0m
    [0;35mprotected[0m [0;39mparams[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mdata[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mquery[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mcookies[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_environment[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mbase[0m[0;90m => [0m[0;32m''[0m
    [0;35mprotected[0m [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
    [0;35mprotected[0m [0;39mtrustedProxies[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_detectors[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_detectorCache[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\PhpInputStream[0m[0;90m) id:[0m[1;34m10[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39muri[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\Uri[0m[0;90m) id:[0m[1;34m11[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39msession[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session[0m[0;90m) id:[0m[1;34m12[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39mattributes[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39memulatedAttributes[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39muploadedFiles[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mprotocol[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39mrequestTarget[0m[0;90m => [0m[1;33mnull[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mresponse[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Response[0m[0;90m) id:[0m[1;34m13[0m[0;90m {[0m
    [0;39m'status'[0m[0;90m => [0m[0;35m(int)[0m [1;34m200[0m
    [0;39m'contentType'[0m[0;90m => [0m[0;32m'text/html'[0m
    [0;39m'headers'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'Content-Type'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m
    [0;90m][0m
    [0;39m'file'[0m[0;90m => [0m[1;33mnull[0m
    [0;39m'fileRange'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
    [0;39m'cookies'[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Cookie\CookieCollection[0m[0;90m) id:[0m[1;34m14[0m[0;90m {[0m[0;90m}[0m
    [0;39m'cacheDirectives'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
    [0;39m'body'[0m[0;90m => [0m[0;32m''[0m
    [0;35mprotected[0m [0;39m_statusCodes[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_mimeTypes[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_status[0m[0;90m => [0m[0;35m(int)[0m [1;34m200[0m
    [0;35mprotected[0m [0;39m_file[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_fileRange[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_charset[0m[0;90m => [0m[0;32m'UTF-8'[0m
    [0;35mprotected[0m [0;39m_cacheDirectives[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_cookies[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Cookie\CookieCollection[0m[0;90m) id:[0m[1;34m14[0m[0;90m {}[0m
    [0;35mprotected[0m [0;39m_reasonPhrase[0m[0;90m => [0m[0;32m'OK'[0m
    [0;35mprotected[0m [0;39m_streamMode[0m[0;90m => [0m[0;32m'wb+'[0m
    [0;35mprotected[0m [0;39m_streamTarget[0m[0;90m => [0m[0;32m'php://memory'[0m
    [0;35mprotected[0m [0;39mheaders[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mheaderNames[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprivate[0m [0;39mprotocol[0m[0;90m => [0m[0;32m'1.1'[0m
    [0;35mprivate[0m [0;39mstream[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\Stream[0m[0;90m) id:[0m[1;34m15[0m[0;90m {[0m[0;90m}[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39m_responseClass[0m[0;90m => [0m[0;32m'Cake\Http\Response'[0m
  [0;35mprotected[0m [0;39mautoRender[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprotected[0m [0;39m_components[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ComponentRegistry[0m[0;90m) id:[0m[1;34m2[0m[0;90m {}[0m
  [0;35mprotected[0m [0;39mplugin[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39m_eventManager[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Event\EventManager[0m[0;90m) id:[0m[1;34m8[0m[0;90m {}[0m
  [0;35mprotected[0m [0;39m_eventClass[0m[0;90m => [0m[0;32m'Cake\Event\Event'[0m
  [0;35mprotected[0m [0;39m_tableLocator[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\ORM\Locator\TableLocator[0m[0;90m) id:[0m[1;34m16[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mlocations[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_config[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39minstances[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_fallbacked[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39moptions[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mmodelClass[0m[0;90m => [0m[0;32m'Wiki'[0m
  [0;35mprotected[0m [0;39m_modelFactories[0m[0;90m => [0m[0;90m[[0m
    [0;32m'Table'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\ORM\Locator\TableLocator[0m[0;90m) id:[0m[1;34m16[0m[0;90m {}[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'get'[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_modelType[0m[0;90m => [0m[0;32m'Table'[0m
  [0;35mprotected[0m [0;39m_viewBuilder[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\View\ViewBuilder[0m[0;90m) id:[0m[1;34m17[0m[0;90m {[0m
    [0;35mprotected[0m [0;39m_templatePath[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_template[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_plugin[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_theme[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_layout[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_autoLayout[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_layoutPath[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_name[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_className[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_options[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_helpers[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_vars[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
  [0;90m}[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-8" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/Http/Runner.php&amp;line=77">CORE/src/Http/Runner.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-8">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="73"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$middleware</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">process</span><span style="color: #007700">(</span><span style="color: #0000BB">$request</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="74"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="75"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="76"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">fallbackHandler</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="77"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">fallbackHandler</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">handle</span><span style="color: #007700">(</span><span style="color: #0000BB">$request</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="78"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="79"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="80"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$response&nbsp;</span><span style="color: #007700">=&nbsp;new&nbsp;</span><span style="color: #0000BB">Response</span><span style="color: #007700">([</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="81"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #DD0000">'body'&nbsp;</span><span style="color: #007700">=&gt;&nbsp;</span><span style="color: #DD0000">'Middleware&nbsp;queue&nbsp;was&nbsp;exhausted&nbsp;without&nbsp;returning&nbsp;a&nbsp;response&nbsp;'</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-8" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Http\ServerRequest[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;39mtrustProxy[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprotected[0m [0;39mparams[0m[0;90m => [0m[0;90m[[0m
    [0;32m'pass'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m[0;90m,[0m
    [0;32m'controller'[0m[0;90m => [0m[0;32m'Wiki'[0m[0;90m,[0m
    [0;32m'action'[0m[0;90m => [0m[0;32m'index'[0m[0;90m,[0m
    [0;32m'plugin'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'_matchedRoute'[0m[0;90m => [0m[0;32m'/wiki/index'[0m[0;90m,[0m
    [0;32m'?'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'database'[0m[0;90m => [0m[0;32m'test_projects'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'_ext'[0m[0;90m => [0m[1;33mnull[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mdata[0m[0;90m => [0m[0;90m[[0m
    [0;32m'_Token'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'fields'[0m[0;90m => [0m[0;32m'62e0f0cc58dc79c574b8d586cb2d68786d53ed8c%3A'[0m[0;90m,[0m
      [0;32m'unlocked'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
      [0;32m'debug'[0m[0;90m => [0m[0;32m'FormProtector debug data would be added here'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'_csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mquery[0m[0;90m => [0m[0;90m[[0m
    [0;32m'database'[0m[0;90m => [0m[0;32m'test_projects'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mcookies[0m[0;90m => [0m[0;90m[[0m
    [0;32m'csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_environment[0m[0;90m => [0m[0;90m[[0m
    [0;32m'PATH'[0m[0;90m => [0m[0;32m'/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin'[0m[0;90m,[0m
    [0;32m'HOSTNAME'[0m[0;90m => [0m[0;32m'8459499160cb'[0m[0;90m,[0m
    [0;32m'MYSQL_ROOT_PASSWORD'[0m[0;90m => [0m[0;32m'root'[0m[0;90m,[0m
    [0;32m'PHPIZE_DEPS'[0m[0;90m => [0m[0;32m'autoconf 		dpkg-dev 		file 		g++ 		gcc 		libc-dev 		make 		pkg-config 		re2c'[0m[0;90m,[0m
    [0;32m'PHP_INI_DIR'[0m[0;90m => [0m[0;32m'/usr/local/etc/php'[0m[0;90m,[0m
    [0;32m'APACHE_CONFDIR'[0m[0;90m => [0m[0;32m'/etc/apache2'[0m[0;90m,[0m
    [0;32m'APACHE_ENVVARS'[0m[0;90m => [0m[0;32m'/etc/apache2/envvars'[0m[0;90m,[0m
    [0;32m'PHP_CFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_CPPFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_LDFLAGS'[0m[0;90m => [0m[0;32m'-Wl,-O1 -pie'[0m[0;90m,[0m
    [0;32m'GPG_KEYS'[0m[0;90m => [0m[0;32m'42670A7FE4D0441C8E4632349E4FDC074A4EF02D 5A52880781F755608BF815FC910DEB46F53EA312'[0m[0;90m,[0m
    [0;32m'PHP_VERSION'[0m[0;90m => [0m[0;32m'7.4.25'[0m[0;90m,[0m
    [0;32m'PHP_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz'[0m[0;90m,[0m
    [0;32m'PHP_ASC_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz.asc'[0m[0;90m,[0m
    [0;32m'PHP_SHA256'[0m[0;90m => [0m[0;32m'12a758f1d7fee544387a28d3cf73226f47e3a52fb3049f07fcc37d156d393c0a'[0m[0;90m,[0m
    [0;32m'TZ'[0m[0;90m => [0m[0;32m'Europe/Berlin'[0m[0;90m,[0m
    [0;32m'IDE_PHPUNIT_CUSTOM_LOADER'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/autoload.php'[0m[0;90m,[0m
    [0;32m'JETBRAINS_REMOTE_RUN'[0m[0;90m => [0m[0;32m'1'[0m[0;90m,[0m
    [0;32m'TERM'[0m[0;90m => [0m[0;32m'xterm'[0m[0;90m,[0m
    [0;32m'HOME'[0m[0;90m => [0m[0;32m'/root'[0m[0;90m,[0m
    [0;32m'PHP_SELF'[0m[0;90m => [0m[0;32m'/'[0m[0;90m,[0m
    [0;32m'SCRIPT_NAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'SCRIPT_FILENAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'PATH_TRANSLATED'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'DOCUMENT_ROOT'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
    [0;32m'REQUEST_TIME_FLOAT'[0m[0;90m => [0m[0;35m(float)[0m [1;34m1637855853.9672[0m[0;90m,[0m
    [0;32m'REQUEST_TIME'[0m[0;90m => [0m[0;35m(int)[0m [1;34m1637855853[0m[0;90m,[0m
    [0;32m'argv'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'--configuration'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'/var/www/html/phpunit.xml.dist'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'--teamcity'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'argc'[0m[0;90m => [0m[0;35m(int)[0m [1;34m4[0m[0;90m,[0m
    [0;32m'REQUEST_URI'[0m[0;90m => [0m[0;32m'/wiki/index'[0m[0;90m,[0m
    [0;32m'REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m[0;90m,[0m
    [0;32m'QUERY_STRING'[0m[0;90m => [0m[0;32m'database=projects'[0m[0;90m,[0m
    [0;32m'HTTPS'[0m[0;90m => [0m[0;32m'on'[0m[0;90m,[0m
    [0;32m'ORIGINAL_REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mbase[0m[0;90m => [0m[0;32m''[0m
  [0;35mprotected[0m [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
  [0;35mprotected[0m [0;39mtrustedProxies[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39m_detectors[0m[0;90m => [0m[0;90m[[0m
    [0;32m'get'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'GET'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'post'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'POST'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'put'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PUT'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'patch'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PATCH'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'delete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'DELETE'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'head'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'HEAD'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'OPTIONS'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ssl'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTPS'[0m[0;90m,[0m
      [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ajax'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTP_X_REQUESTED_WITH'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'XMLHttpRequest'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'json'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'json'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'xml'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'xml'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'mobile'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'tablet'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'csv'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'csv'[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_detectorCache[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\PhpInputStream[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mresource[0m[0;90m => [0m(resource) Resource id #3107
    [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;32m'php://input'[0m
    [0;35mprivate[0m [0;39mcache[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mreachedEof[0m[0;90m => [0m[1;33mfalse[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39muri[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\Uri[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m
    [0;39mbase[0m[0;90m => [0m[0;32m''[0m
    [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
    [0;35mprotected[0m [0;39mallowedSchemes[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprivate[0m [0;39mscheme[0m[0;90m => [0m[0;32m'https'[0m
    [0;35mprivate[0m [0;39muserInfo[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mhost[0m[0;90m => [0m[0;32m'localhost'[0m
    [0;35mprivate[0m [0;39mport[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprivate[0m [0;39mpath[0m[0;90m => [0m[0;32m'/wiki/index'[0m
    [0;35mprivate[0m [0;39mquery[0m[0;90m => [0m[0;32m'database=projects'[0m
    [0;35mprivate[0m [0;39mfragment[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39muriString[0m[0;90m => [0m[1;33mnull[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39msession[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m
    [0;35mprotected[0m [0;39m_engine[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session\CacheSession[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_started[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_lifetime[0m[0;90m => [0m[0;35m(int)[0m [1;34m1440[0m
    [0;35mprotected[0m [0;39m_isCLI[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mattributes[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39memulatedAttributes[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'session'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'webroot'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'base'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'params'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;32m'here'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39muploadedFiles[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mprotocol[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39mrequestTarget[0m[0;90m => [0m[1;33mnull[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-9" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/Http/Middleware/BodyParserMiddleware.php&amp;line=159">CORE/src/Http/Middleware/BodyParserMiddleware.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-9">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="155"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">*/</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="156"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">public&nbsp;function&nbsp;</span><span style="color: #0000BB">process</span><span style="color: #007700">(</span><span style="color: #0000BB">ServerRequestInterface&nbsp;$request</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">RequestHandlerInterface&nbsp;$handler</span><span style="color: #007700">):&nbsp;</span><span style="color: #0000BB">ResponseInterface</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="157"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="158"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(!</span><span style="color: #0000BB">in_array</span><span style="color: #007700">(</span><span style="color: #0000BB">$request</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">getMethod</span><span style="color: #007700">(),&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">methods</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">true</span><span style="color: #007700">))&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="159"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$handler</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">handle</span><span style="color: #007700">(</span><span style="color: #0000BB">$request</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="160"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="161"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">[</span><span style="color: #0000BB">$type</span><span style="color: #007700">]&nbsp;=&nbsp;</span><span style="color: #0000BB">explode</span><span style="color: #007700">(</span><span style="color: #DD0000">';'</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">$request</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">getHeaderLine</span><span style="color: #007700">(</span><span style="color: #DD0000">'Content-Type'</span><span style="color: #007700">));</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="162"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$type&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">strtolower</span><span style="color: #007700">(</span><span style="color: #0000BB">$type</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="163"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(!isset(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">parsers</span><span style="color: #007700">[</span><span style="color: #0000BB">$type</span><span style="color: #007700">]))&nbsp;{</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-9" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Http\ServerRequest[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;39mtrustProxy[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprotected[0m [0;39mparams[0m[0;90m => [0m[0;90m[[0m
    [0;32m'pass'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m[0;90m,[0m
    [0;32m'controller'[0m[0;90m => [0m[0;32m'Wiki'[0m[0;90m,[0m
    [0;32m'action'[0m[0;90m => [0m[0;32m'index'[0m[0;90m,[0m
    [0;32m'plugin'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'_matchedRoute'[0m[0;90m => [0m[0;32m'/wiki/index'[0m[0;90m,[0m
    [0;32m'?'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'database'[0m[0;90m => [0m[0;32m'test_projects'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'_ext'[0m[0;90m => [0m[1;33mnull[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mdata[0m[0;90m => [0m[0;90m[[0m
    [0;32m'_Token'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'fields'[0m[0;90m => [0m[0;32m'62e0f0cc58dc79c574b8d586cb2d68786d53ed8c%3A'[0m[0;90m,[0m
      [0;32m'unlocked'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
      [0;32m'debug'[0m[0;90m => [0m[0;32m'FormProtector debug data would be added here'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'_csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mquery[0m[0;90m => [0m[0;90m[[0m
    [0;32m'database'[0m[0;90m => [0m[0;32m'test_projects'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mcookies[0m[0;90m => [0m[0;90m[[0m
    [0;32m'csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_environment[0m[0;90m => [0m[0;90m[[0m
    [0;32m'PATH'[0m[0;90m => [0m[0;32m'/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin'[0m[0;90m,[0m
    [0;32m'HOSTNAME'[0m[0;90m => [0m[0;32m'8459499160cb'[0m[0;90m,[0m
    [0;32m'MYSQL_ROOT_PASSWORD'[0m[0;90m => [0m[0;32m'root'[0m[0;90m,[0m
    [0;32m'PHPIZE_DEPS'[0m[0;90m => [0m[0;32m'autoconf 		dpkg-dev 		file 		g++ 		gcc 		libc-dev 		make 		pkg-config 		re2c'[0m[0;90m,[0m
    [0;32m'PHP_INI_DIR'[0m[0;90m => [0m[0;32m'/usr/local/etc/php'[0m[0;90m,[0m
    [0;32m'APACHE_CONFDIR'[0m[0;90m => [0m[0;32m'/etc/apache2'[0m[0;90m,[0m
    [0;32m'APACHE_ENVVARS'[0m[0;90m => [0m[0;32m'/etc/apache2/envvars'[0m[0;90m,[0m
    [0;32m'PHP_CFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_CPPFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_LDFLAGS'[0m[0;90m => [0m[0;32m'-Wl,-O1 -pie'[0m[0;90m,[0m
    [0;32m'GPG_KEYS'[0m[0;90m => [0m[0;32m'42670A7FE4D0441C8E4632349E4FDC074A4EF02D 5A52880781F755608BF815FC910DEB46F53EA312'[0m[0;90m,[0m
    [0;32m'PHP_VERSION'[0m[0;90m => [0m[0;32m'7.4.25'[0m[0;90m,[0m
    [0;32m'PHP_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz'[0m[0;90m,[0m
    [0;32m'PHP_ASC_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz.asc'[0m[0;90m,[0m
    [0;32m'PHP_SHA256'[0m[0;90m => [0m[0;32m'12a758f1d7fee544387a28d3cf73226f47e3a52fb3049f07fcc37d156d393c0a'[0m[0;90m,[0m
    [0;32m'TZ'[0m[0;90m => [0m[0;32m'Europe/Berlin'[0m[0;90m,[0m
    [0;32m'IDE_PHPUNIT_CUSTOM_LOADER'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/autoload.php'[0m[0;90m,[0m
    [0;32m'JETBRAINS_REMOTE_RUN'[0m[0;90m => [0m[0;32m'1'[0m[0;90m,[0m
    [0;32m'TERM'[0m[0;90m => [0m[0;32m'xterm'[0m[0;90m,[0m
    [0;32m'HOME'[0m[0;90m => [0m[0;32m'/root'[0m[0;90m,[0m
    [0;32m'PHP_SELF'[0m[0;90m => [0m[0;32m'/'[0m[0;90m,[0m
    [0;32m'SCRIPT_NAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'SCRIPT_FILENAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'PATH_TRANSLATED'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'DOCUMENT_ROOT'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
    [0;32m'REQUEST_TIME_FLOAT'[0m[0;90m => [0m[0;35m(float)[0m [1;34m1637855853.9672[0m[0;90m,[0m
    [0;32m'REQUEST_TIME'[0m[0;90m => [0m[0;35m(int)[0m [1;34m1637855853[0m[0;90m,[0m
    [0;32m'argv'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'--configuration'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'/var/www/html/phpunit.xml.dist'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'--teamcity'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'argc'[0m[0;90m => [0m[0;35m(int)[0m [1;34m4[0m[0;90m,[0m
    [0;32m'REQUEST_URI'[0m[0;90m => [0m[0;32m'/wiki/index'[0m[0;90m,[0m
    [0;32m'REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m[0;90m,[0m
    [0;32m'QUERY_STRING'[0m[0;90m => [0m[0;32m'database=projects'[0m[0;90m,[0m
    [0;32m'HTTPS'[0m[0;90m => [0m[0;32m'on'[0m[0;90m,[0m
    [0;32m'ORIGINAL_REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mbase[0m[0;90m => [0m[0;32m''[0m
  [0;35mprotected[0m [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
  [0;35mprotected[0m [0;39mtrustedProxies[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39m_detectors[0m[0;90m => [0m[0;90m[[0m
    [0;32m'get'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'GET'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'post'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'POST'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'put'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PUT'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'patch'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PATCH'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'delete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'DELETE'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'head'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'HEAD'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'OPTIONS'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ssl'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTPS'[0m[0;90m,[0m
      [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ajax'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTP_X_REQUESTED_WITH'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'XMLHttpRequest'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'json'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'json'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'xml'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'xml'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'mobile'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'tablet'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'csv'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'csv'[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_detectorCache[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\PhpInputStream[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mresource[0m[0;90m => [0m(resource) Resource id #3107
    [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;32m'php://input'[0m
    [0;35mprivate[0m [0;39mcache[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mreachedEof[0m[0;90m => [0m[1;33mfalse[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39muri[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\Uri[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m
    [0;39mbase[0m[0;90m => [0m[0;32m''[0m
    [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
    [0;35mprotected[0m [0;39mallowedSchemes[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprivate[0m [0;39mscheme[0m[0;90m => [0m[0;32m'https'[0m
    [0;35mprivate[0m [0;39muserInfo[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mhost[0m[0;90m => [0m[0;32m'localhost'[0m
    [0;35mprivate[0m [0;39mport[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprivate[0m [0;39mpath[0m[0;90m => [0m[0;32m'/wiki/index'[0m
    [0;35mprivate[0m [0;39mquery[0m[0;90m => [0m[0;32m'database=projects'[0m
    [0;35mprivate[0m [0;39mfragment[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39muriString[0m[0;90m => [0m[1;33mnull[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39msession[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m
    [0;35mprotected[0m [0;39m_engine[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session\CacheSession[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_started[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_lifetime[0m[0;90m => [0m[0;35m(int)[0m [1;34m1440[0m
    [0;35mprotected[0m [0;39m_isCLI[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mattributes[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39memulatedAttributes[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'session'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'webroot'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'base'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'params'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;32m'here'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39muploadedFiles[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mprotocol[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39mrequestTarget[0m[0;90m => [0m[1;33mnull[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-10" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/Http/Runner.php&amp;line=73">CORE/src/Http/Runner.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-10">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="69"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">queue</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">valid</span><span style="color: #007700">())&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="70"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$middleware&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">queue</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">current</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="71"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">queue</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">next</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="72"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="73"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$middleware</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">process</span><span style="color: #007700">(</span><span style="color: #0000BB">$request</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="74"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="75"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="76"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">fallbackHandler</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="77"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">fallbackHandler</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">handle</span><span style="color: #007700">(</span><span style="color: #0000BB">$request</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-10" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Http\ServerRequest[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;39mtrustProxy[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprotected[0m [0;39mparams[0m[0;90m => [0m[0;90m[[0m
    [0;32m'pass'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m[0;90m,[0m
    [0;32m'controller'[0m[0;90m => [0m[0;32m'Wiki'[0m[0;90m,[0m
    [0;32m'action'[0m[0;90m => [0m[0;32m'index'[0m[0;90m,[0m
    [0;32m'plugin'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'_matchedRoute'[0m[0;90m => [0m[0;32m'/wiki/index'[0m[0;90m,[0m
    [0;32m'?'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'database'[0m[0;90m => [0m[0;32m'test_projects'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'_ext'[0m[0;90m => [0m[1;33mnull[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mdata[0m[0;90m => [0m[0;90m[[0m
    [0;32m'_Token'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'fields'[0m[0;90m => [0m[0;32m'62e0f0cc58dc79c574b8d586cb2d68786d53ed8c%3A'[0m[0;90m,[0m
      [0;32m'unlocked'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
      [0;32m'debug'[0m[0;90m => [0m[0;32m'FormProtector debug data would be added here'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'_csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mquery[0m[0;90m => [0m[0;90m[[0m
    [0;32m'database'[0m[0;90m => [0m[0;32m'test_projects'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mcookies[0m[0;90m => [0m[0;90m[[0m
    [0;32m'csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_environment[0m[0;90m => [0m[0;90m[[0m
    [0;32m'PATH'[0m[0;90m => [0m[0;32m'/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin'[0m[0;90m,[0m
    [0;32m'HOSTNAME'[0m[0;90m => [0m[0;32m'8459499160cb'[0m[0;90m,[0m
    [0;32m'MYSQL_ROOT_PASSWORD'[0m[0;90m => [0m[0;32m'root'[0m[0;90m,[0m
    [0;32m'PHPIZE_DEPS'[0m[0;90m => [0m[0;32m'autoconf 		dpkg-dev 		file 		g++ 		gcc 		libc-dev 		make 		pkg-config 		re2c'[0m[0;90m,[0m
    [0;32m'PHP_INI_DIR'[0m[0;90m => [0m[0;32m'/usr/local/etc/php'[0m[0;90m,[0m
    [0;32m'APACHE_CONFDIR'[0m[0;90m => [0m[0;32m'/etc/apache2'[0m[0;90m,[0m
    [0;32m'APACHE_ENVVARS'[0m[0;90m => [0m[0;32m'/etc/apache2/envvars'[0m[0;90m,[0m
    [0;32m'PHP_CFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_CPPFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_LDFLAGS'[0m[0;90m => [0m[0;32m'-Wl,-O1 -pie'[0m[0;90m,[0m
    [0;32m'GPG_KEYS'[0m[0;90m => [0m[0;32m'42670A7FE4D0441C8E4632349E4FDC074A4EF02D 5A52880781F755608BF815FC910DEB46F53EA312'[0m[0;90m,[0m
    [0;32m'PHP_VERSION'[0m[0;90m => [0m[0;32m'7.4.25'[0m[0;90m,[0m
    [0;32m'PHP_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz'[0m[0;90m,[0m
    [0;32m'PHP_ASC_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz.asc'[0m[0;90m,[0m
    [0;32m'PHP_SHA256'[0m[0;90m => [0m[0;32m'12a758f1d7fee544387a28d3cf73226f47e3a52fb3049f07fcc37d156d393c0a'[0m[0;90m,[0m
    [0;32m'TZ'[0m[0;90m => [0m[0;32m'Europe/Berlin'[0m[0;90m,[0m
    [0;32m'IDE_PHPUNIT_CUSTOM_LOADER'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/autoload.php'[0m[0;90m,[0m
    [0;32m'JETBRAINS_REMOTE_RUN'[0m[0;90m => [0m[0;32m'1'[0m[0;90m,[0m
    [0;32m'TERM'[0m[0;90m => [0m[0;32m'xterm'[0m[0;90m,[0m
    [0;32m'HOME'[0m[0;90m => [0m[0;32m'/root'[0m[0;90m,[0m
    [0;32m'PHP_SELF'[0m[0;90m => [0m[0;32m'/'[0m[0;90m,[0m
    [0;32m'SCRIPT_NAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'SCRIPT_FILENAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'PATH_TRANSLATED'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'DOCUMENT_ROOT'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
    [0;32m'REQUEST_TIME_FLOAT'[0m[0;90m => [0m[0;35m(float)[0m [1;34m1637855853.9672[0m[0;90m,[0m
    [0;32m'REQUEST_TIME'[0m[0;90m => [0m[0;35m(int)[0m [1;34m1637855853[0m[0;90m,[0m
    [0;32m'argv'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'--configuration'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'/var/www/html/phpunit.xml.dist'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'--teamcity'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'argc'[0m[0;90m => [0m[0;35m(int)[0m [1;34m4[0m[0;90m,[0m
    [0;32m'REQUEST_URI'[0m[0;90m => [0m[0;32m'/wiki/index'[0m[0;90m,[0m
    [0;32m'REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m[0;90m,[0m
    [0;32m'QUERY_STRING'[0m[0;90m => [0m[0;32m'database=projects'[0m[0;90m,[0m
    [0;32m'HTTPS'[0m[0;90m => [0m[0;32m'on'[0m[0;90m,[0m
    [0;32m'ORIGINAL_REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mbase[0m[0;90m => [0m[0;32m''[0m
  [0;35mprotected[0m [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
  [0;35mprotected[0m [0;39mtrustedProxies[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39m_detectors[0m[0;90m => [0m[0;90m[[0m
    [0;32m'get'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'GET'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'post'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'POST'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'put'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PUT'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'patch'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PATCH'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'delete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'DELETE'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'head'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'HEAD'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'OPTIONS'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ssl'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTPS'[0m[0;90m,[0m
      [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ajax'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTP_X_REQUESTED_WITH'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'XMLHttpRequest'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'json'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'json'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'xml'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'xml'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'mobile'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'tablet'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'csv'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'csv'[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_detectorCache[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\PhpInputStream[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mresource[0m[0;90m => [0m(resource) Resource id #3107
    [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;32m'php://input'[0m
    [0;35mprivate[0m [0;39mcache[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mreachedEof[0m[0;90m => [0m[1;33mfalse[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39muri[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\Uri[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m
    [0;39mbase[0m[0;90m => [0m[0;32m''[0m
    [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
    [0;35mprotected[0m [0;39mallowedSchemes[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprivate[0m [0;39mscheme[0m[0;90m => [0m[0;32m'https'[0m
    [0;35mprivate[0m [0;39muserInfo[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mhost[0m[0;90m => [0m[0;32m'localhost'[0m
    [0;35mprivate[0m [0;39mport[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprivate[0m [0;39mpath[0m[0;90m => [0m[0;32m'/wiki/index'[0m
    [0;35mprivate[0m [0;39mquery[0m[0;90m => [0m[0;32m'database=projects'[0m
    [0;35mprivate[0m [0;39mfragment[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39muriString[0m[0;90m => [0m[1;33mnull[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39msession[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m
    [0;35mprotected[0m [0;39m_engine[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session\CacheSession[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_started[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_lifetime[0m[0;90m => [0m[0;35m(int)[0m [1;34m1440[0m
    [0;35mprotected[0m [0;39m_isCLI[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mattributes[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39memulatedAttributes[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'session'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'webroot'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'base'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'params'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;32m'here'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39muploadedFiles[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mprotocol[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39mrequestTarget[0m[0;90m => [0m[1;33mnull[0m
[0;90m}[0m</div>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Http\Runner[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;35mprotected[0m [0;39mqueue[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\MiddlewareQueue[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mposition[0m[0;90m => [0m[0;35m(int)[0m [1;34m5[0m
    [0;35mprotected[0m [0;39mqueue[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mfallbackHandler[0m[0;90m => [0m[0;90mobject([0m[0;36mApp\Application[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mconfigDir[0m[0;90m => [0m[0;32m'/var/www/html/config/'[0m
    [0;35mprotected[0m [0;39mplugins[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Core\PluginCollection[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39mcontrollerFactory[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ControllerFactory[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_eventManager[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Event\EventManager[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_eventClass[0m[0;90m => [0m[0;32m'Cake\Event\Event'[0m
  [0;90m}[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-11" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/Routing/Middleware/RoutingMiddleware.php&amp;line=166">CORE/src/Routing/Middleware/RoutingMiddleware.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-11">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="162"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="163"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="164"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$matching&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">Router</span><span style="color: #007700">::</span><span style="color: #0000BB">getRouteCollection</span><span style="color: #007700">()-&gt;</span><span style="color: #0000BB">getMiddleware</span><span style="color: #007700">(</span><span style="color: #0000BB">$middleware</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="165"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(!</span><span style="color: #0000BB">$matching</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="166"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$handler</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">handle</span><span style="color: #007700">(</span><span style="color: #0000BB">$request</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="167"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="168"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="169"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$middleware&nbsp;</span><span style="color: #007700">=&nbsp;new&nbsp;</span><span style="color: #0000BB">MiddlewareQueue</span><span style="color: #007700">(</span><span style="color: #0000BB">$matching</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="170"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$runner&nbsp;</span><span style="color: #007700">=&nbsp;new&nbsp;</span><span style="color: #0000BB">Runner</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-11" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Http\ServerRequest[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;39mtrustProxy[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprotected[0m [0;39mparams[0m[0;90m => [0m[0;90m[[0m
    [0;32m'pass'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m[0;90m,[0m
    [0;32m'controller'[0m[0;90m => [0m[0;32m'Wiki'[0m[0;90m,[0m
    [0;32m'action'[0m[0;90m => [0m[0;32m'index'[0m[0;90m,[0m
    [0;32m'plugin'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'_matchedRoute'[0m[0;90m => [0m[0;32m'/wiki/index'[0m[0;90m,[0m
    [0;32m'?'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'database'[0m[0;90m => [0m[0;32m'test_projects'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'_ext'[0m[0;90m => [0m[1;33mnull[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mdata[0m[0;90m => [0m[0;90m[[0m
    [0;32m'_Token'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'fields'[0m[0;90m => [0m[0;32m'62e0f0cc58dc79c574b8d586cb2d68786d53ed8c%3A'[0m[0;90m,[0m
      [0;32m'unlocked'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
      [0;32m'debug'[0m[0;90m => [0m[0;32m'FormProtector debug data would be added here'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'_csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mquery[0m[0;90m => [0m[0;90m[[0m
    [0;32m'database'[0m[0;90m => [0m[0;32m'test_projects'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mcookies[0m[0;90m => [0m[0;90m[[0m
    [0;32m'csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_environment[0m[0;90m => [0m[0;90m[[0m
    [0;32m'PATH'[0m[0;90m => [0m[0;32m'/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin'[0m[0;90m,[0m
    [0;32m'HOSTNAME'[0m[0;90m => [0m[0;32m'8459499160cb'[0m[0;90m,[0m
    [0;32m'MYSQL_ROOT_PASSWORD'[0m[0;90m => [0m[0;32m'root'[0m[0;90m,[0m
    [0;32m'PHPIZE_DEPS'[0m[0;90m => [0m[0;32m'autoconf 		dpkg-dev 		file 		g++ 		gcc 		libc-dev 		make 		pkg-config 		re2c'[0m[0;90m,[0m
    [0;32m'PHP_INI_DIR'[0m[0;90m => [0m[0;32m'/usr/local/etc/php'[0m[0;90m,[0m
    [0;32m'APACHE_CONFDIR'[0m[0;90m => [0m[0;32m'/etc/apache2'[0m[0;90m,[0m
    [0;32m'APACHE_ENVVARS'[0m[0;90m => [0m[0;32m'/etc/apache2/envvars'[0m[0;90m,[0m
    [0;32m'PHP_CFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_CPPFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_LDFLAGS'[0m[0;90m => [0m[0;32m'-Wl,-O1 -pie'[0m[0;90m,[0m
    [0;32m'GPG_KEYS'[0m[0;90m => [0m[0;32m'42670A7FE4D0441C8E4632349E4FDC074A4EF02D 5A52880781F755608BF815FC910DEB46F53EA312'[0m[0;90m,[0m
    [0;32m'PHP_VERSION'[0m[0;90m => [0m[0;32m'7.4.25'[0m[0;90m,[0m
    [0;32m'PHP_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz'[0m[0;90m,[0m
    [0;32m'PHP_ASC_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz.asc'[0m[0;90m,[0m
    [0;32m'PHP_SHA256'[0m[0;90m => [0m[0;32m'12a758f1d7fee544387a28d3cf73226f47e3a52fb3049f07fcc37d156d393c0a'[0m[0;90m,[0m
    [0;32m'TZ'[0m[0;90m => [0m[0;32m'Europe/Berlin'[0m[0;90m,[0m
    [0;32m'IDE_PHPUNIT_CUSTOM_LOADER'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/autoload.php'[0m[0;90m,[0m
    [0;32m'JETBRAINS_REMOTE_RUN'[0m[0;90m => [0m[0;32m'1'[0m[0;90m,[0m
    [0;32m'TERM'[0m[0;90m => [0m[0;32m'xterm'[0m[0;90m,[0m
    [0;32m'HOME'[0m[0;90m => [0m[0;32m'/root'[0m[0;90m,[0m
    [0;32m'PHP_SELF'[0m[0;90m => [0m[0;32m'/'[0m[0;90m,[0m
    [0;32m'SCRIPT_NAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'SCRIPT_FILENAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'PATH_TRANSLATED'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'DOCUMENT_ROOT'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
    [0;32m'REQUEST_TIME_FLOAT'[0m[0;90m => [0m[0;35m(float)[0m [1;34m1637855853.9672[0m[0;90m,[0m
    [0;32m'REQUEST_TIME'[0m[0;90m => [0m[0;35m(int)[0m [1;34m1637855853[0m[0;90m,[0m
    [0;32m'argv'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'--configuration'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'/var/www/html/phpunit.xml.dist'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'--teamcity'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'argc'[0m[0;90m => [0m[0;35m(int)[0m [1;34m4[0m[0;90m,[0m
    [0;32m'REQUEST_URI'[0m[0;90m => [0m[0;32m'/wiki/index'[0m[0;90m,[0m
    [0;32m'REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m[0;90m,[0m
    [0;32m'QUERY_STRING'[0m[0;90m => [0m[0;32m'database=projects'[0m[0;90m,[0m
    [0;32m'HTTPS'[0m[0;90m => [0m[0;32m'on'[0m[0;90m,[0m
    [0;32m'ORIGINAL_REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mbase[0m[0;90m => [0m[0;32m''[0m
  [0;35mprotected[0m [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
  [0;35mprotected[0m [0;39mtrustedProxies[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39m_detectors[0m[0;90m => [0m[0;90m[[0m
    [0;32m'get'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'GET'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'post'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'POST'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'put'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PUT'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'patch'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PATCH'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'delete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'DELETE'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'head'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'HEAD'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'OPTIONS'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ssl'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTPS'[0m[0;90m,[0m
      [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ajax'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTP_X_REQUESTED_WITH'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'XMLHttpRequest'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'json'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'json'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'xml'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'xml'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'mobile'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'tablet'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'csv'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'csv'[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_detectorCache[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\PhpInputStream[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mresource[0m[0;90m => [0m(resource) Resource id #3107
    [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;32m'php://input'[0m
    [0;35mprivate[0m [0;39mcache[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mreachedEof[0m[0;90m => [0m[1;33mfalse[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39muri[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\Uri[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m
    [0;39mbase[0m[0;90m => [0m[0;32m''[0m
    [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
    [0;35mprotected[0m [0;39mallowedSchemes[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprivate[0m [0;39mscheme[0m[0;90m => [0m[0;32m'https'[0m
    [0;35mprivate[0m [0;39muserInfo[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mhost[0m[0;90m => [0m[0;32m'localhost'[0m
    [0;35mprivate[0m [0;39mport[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprivate[0m [0;39mpath[0m[0;90m => [0m[0;32m'/wiki/index'[0m
    [0;35mprivate[0m [0;39mquery[0m[0;90m => [0m[0;32m'database=projects'[0m
    [0;35mprivate[0m [0;39mfragment[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39muriString[0m[0;90m => [0m[1;33mnull[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39msession[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m
    [0;35mprotected[0m [0;39m_engine[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session\CacheSession[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_started[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_lifetime[0m[0;90m => [0m[0;35m(int)[0m [1;34m1440[0m
    [0;35mprotected[0m [0;39m_isCLI[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mattributes[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39memulatedAttributes[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'session'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'webroot'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'base'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'params'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;32m'here'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39muploadedFiles[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mprotocol[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39mrequestTarget[0m[0;90m => [0m[1;33mnull[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-12" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/Http/Runner.php&amp;line=73">CORE/src/Http/Runner.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-12">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="69"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">queue</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">valid</span><span style="color: #007700">())&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="70"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$middleware&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">queue</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">current</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="71"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">queue</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">next</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="72"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="73"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$middleware</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">process</span><span style="color: #007700">(</span><span style="color: #0000BB">$request</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="74"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="75"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="76"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">fallbackHandler</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="77"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">fallbackHandler</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">handle</span><span style="color: #007700">(</span><span style="color: #0000BB">$request</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-12" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Http\ServerRequest[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;39mtrustProxy[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprotected[0m [0;39mparams[0m[0;90m => [0m[0;90m[[0m
    [0;32m'pass'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m[0;90m,[0m
    [0;32m'controller'[0m[0;90m => [0m[0;32m'Wiki'[0m[0;90m,[0m
    [0;32m'action'[0m[0;90m => [0m[0;32m'index'[0m[0;90m,[0m
    [0;32m'plugin'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'_matchedRoute'[0m[0;90m => [0m[0;32m'/wiki/index'[0m[0;90m,[0m
    [0;32m'?'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'database'[0m[0;90m => [0m[0;32m'test_projects'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'_ext'[0m[0;90m => [0m[1;33mnull[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mdata[0m[0;90m => [0m[0;90m[[0m
    [0;32m'_Token'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'fields'[0m[0;90m => [0m[0;32m'62e0f0cc58dc79c574b8d586cb2d68786d53ed8c%3A'[0m[0;90m,[0m
      [0;32m'unlocked'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
      [0;32m'debug'[0m[0;90m => [0m[0;32m'FormProtector debug data would be added here'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'_csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mquery[0m[0;90m => [0m[0;90m[[0m
    [0;32m'database'[0m[0;90m => [0m[0;32m'test_projects'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mcookies[0m[0;90m => [0m[0;90m[[0m
    [0;32m'csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_environment[0m[0;90m => [0m[0;90m[[0m
    [0;32m'PATH'[0m[0;90m => [0m[0;32m'/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin'[0m[0;90m,[0m
    [0;32m'HOSTNAME'[0m[0;90m => [0m[0;32m'8459499160cb'[0m[0;90m,[0m
    [0;32m'MYSQL_ROOT_PASSWORD'[0m[0;90m => [0m[0;32m'root'[0m[0;90m,[0m
    [0;32m'PHPIZE_DEPS'[0m[0;90m => [0m[0;32m'autoconf 		dpkg-dev 		file 		g++ 		gcc 		libc-dev 		make 		pkg-config 		re2c'[0m[0;90m,[0m
    [0;32m'PHP_INI_DIR'[0m[0;90m => [0m[0;32m'/usr/local/etc/php'[0m[0;90m,[0m
    [0;32m'APACHE_CONFDIR'[0m[0;90m => [0m[0;32m'/etc/apache2'[0m[0;90m,[0m
    [0;32m'APACHE_ENVVARS'[0m[0;90m => [0m[0;32m'/etc/apache2/envvars'[0m[0;90m,[0m
    [0;32m'PHP_CFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_CPPFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_LDFLAGS'[0m[0;90m => [0m[0;32m'-Wl,-O1 -pie'[0m[0;90m,[0m
    [0;32m'GPG_KEYS'[0m[0;90m => [0m[0;32m'42670A7FE4D0441C8E4632349E4FDC074A4EF02D 5A52880781F755608BF815FC910DEB46F53EA312'[0m[0;90m,[0m
    [0;32m'PHP_VERSION'[0m[0;90m => [0m[0;32m'7.4.25'[0m[0;90m,[0m
    [0;32m'PHP_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz'[0m[0;90m,[0m
    [0;32m'PHP_ASC_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz.asc'[0m[0;90m,[0m
    [0;32m'PHP_SHA256'[0m[0;90m => [0m[0;32m'12a758f1d7fee544387a28d3cf73226f47e3a52fb3049f07fcc37d156d393c0a'[0m[0;90m,[0m
    [0;32m'TZ'[0m[0;90m => [0m[0;32m'Europe/Berlin'[0m[0;90m,[0m
    [0;32m'IDE_PHPUNIT_CUSTOM_LOADER'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/autoload.php'[0m[0;90m,[0m
    [0;32m'JETBRAINS_REMOTE_RUN'[0m[0;90m => [0m[0;32m'1'[0m[0;90m,[0m
    [0;32m'TERM'[0m[0;90m => [0m[0;32m'xterm'[0m[0;90m,[0m
    [0;32m'HOME'[0m[0;90m => [0m[0;32m'/root'[0m[0;90m,[0m
    [0;32m'PHP_SELF'[0m[0;90m => [0m[0;32m'/'[0m[0;90m,[0m
    [0;32m'SCRIPT_NAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'SCRIPT_FILENAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'PATH_TRANSLATED'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'DOCUMENT_ROOT'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
    [0;32m'REQUEST_TIME_FLOAT'[0m[0;90m => [0m[0;35m(float)[0m [1;34m1637855853.9672[0m[0;90m,[0m
    [0;32m'REQUEST_TIME'[0m[0;90m => [0m[0;35m(int)[0m [1;34m1637855853[0m[0;90m,[0m
    [0;32m'argv'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'--configuration'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'/var/www/html/phpunit.xml.dist'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'--teamcity'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'argc'[0m[0;90m => [0m[0;35m(int)[0m [1;34m4[0m[0;90m,[0m
    [0;32m'REQUEST_URI'[0m[0;90m => [0m[0;32m'/wiki/index'[0m[0;90m,[0m
    [0;32m'REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m[0;90m,[0m
    [0;32m'QUERY_STRING'[0m[0;90m => [0m[0;32m'database=projects'[0m[0;90m,[0m
    [0;32m'HTTPS'[0m[0;90m => [0m[0;32m'on'[0m[0;90m,[0m
    [0;32m'ORIGINAL_REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mbase[0m[0;90m => [0m[0;32m''[0m
  [0;35mprotected[0m [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
  [0;35mprotected[0m [0;39mtrustedProxies[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39m_detectors[0m[0;90m => [0m[0;90m[[0m
    [0;32m'get'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'GET'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'post'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'POST'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'put'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PUT'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'patch'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PATCH'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'delete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'DELETE'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'head'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'HEAD'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'OPTIONS'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ssl'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTPS'[0m[0;90m,[0m
      [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ajax'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTP_X_REQUESTED_WITH'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'XMLHttpRequest'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'json'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'json'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'xml'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'xml'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'mobile'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'tablet'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'csv'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'csv'[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_detectorCache[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\PhpInputStream[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mresource[0m[0;90m => [0m(resource) Resource id #3107
    [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;32m'php://input'[0m
    [0;35mprivate[0m [0;39mcache[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mreachedEof[0m[0;90m => [0m[1;33mfalse[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39muri[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\Uri[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m
    [0;39mbase[0m[0;90m => [0m[0;32m''[0m
    [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
    [0;35mprotected[0m [0;39mallowedSchemes[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprivate[0m [0;39mscheme[0m[0;90m => [0m[0;32m'https'[0m
    [0;35mprivate[0m [0;39muserInfo[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mhost[0m[0;90m => [0m[0;32m'localhost'[0m
    [0;35mprivate[0m [0;39mport[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprivate[0m [0;39mpath[0m[0;90m => [0m[0;32m'/wiki/index'[0m
    [0;35mprivate[0m [0;39mquery[0m[0;90m => [0m[0;32m'database=projects'[0m
    [0;35mprivate[0m [0;39mfragment[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39muriString[0m[0;90m => [0m[1;33mnull[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39msession[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m
    [0;35mprotected[0m [0;39m_engine[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session\CacheSession[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_started[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_lifetime[0m[0;90m => [0m[0;35m(int)[0m [1;34m1440[0m
    [0;35mprotected[0m [0;39m_isCLI[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mattributes[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39memulatedAttributes[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'session'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'webroot'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'base'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'params'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;32m'here'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39muploadedFiles[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mprotocol[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39mrequestTarget[0m[0;90m => [0m[1;33mnull[0m
[0;90m}[0m</div>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Http\Runner[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;35mprotected[0m [0;39mqueue[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\MiddlewareQueue[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mposition[0m[0;90m => [0m[0;35m(int)[0m [1;34m5[0m
    [0;35mprotected[0m [0;39mqueue[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mfallbackHandler[0m[0;90m => [0m[0;90mobject([0m[0;36mApp\Application[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mconfigDir[0m[0;90m => [0m[0;32m'/var/www/html/config/'[0m
    [0;35mprotected[0m [0;39mplugins[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Core\PluginCollection[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39mcontrollerFactory[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ControllerFactory[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_eventManager[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Event\EventManager[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_eventClass[0m[0;90m => [0m[0;32m'Cake\Event\Event'[0m
  [0;90m}[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-13" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/Http/Middleware/HttpsEnforcerMiddleware.php&amp;line=81">CORE/src/Http/Middleware/HttpsEnforcerMiddleware.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-13">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="77"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$request</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">getUri</span><span style="color: #007700">()-&gt;</span><span style="color: #0000BB">getScheme</span><span style="color: #007700">()&nbsp;===&nbsp;</span><span style="color: #DD0000">'https'</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="78"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">||&nbsp;(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">config</span><span style="color: #007700">[</span><span style="color: #DD0000">'disableOnDebug'</span><span style="color: #007700">]</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="79"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">&amp;&amp;&nbsp;</span><span style="color: #0000BB">Configure</span><span style="color: #007700">::</span><span style="color: #0000BB">read</span><span style="color: #007700">(</span><span style="color: #DD0000">'debug'</span><span style="color: #007700">))</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="80"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="81"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$handler</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">handle</span><span style="color: #007700">(</span><span style="color: #0000BB">$request</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="82"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="83"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="84"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">config</span><span style="color: #007700">[</span><span style="color: #DD0000">'redirect'</span><span style="color: #007700">]&nbsp;&amp;&amp;&nbsp;</span><span style="color: #0000BB">$request</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">getMethod</span><span style="color: #007700">()&nbsp;===&nbsp;</span><span style="color: #DD0000">'GET'</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="85"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$uri&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$request</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">getUri</span><span style="color: #007700">()-&gt;</span><span style="color: #0000BB">withScheme</span><span style="color: #007700">(</span><span style="color: #DD0000">'https'</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-13" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Http\ServerRequest[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;39mtrustProxy[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprotected[0m [0;39mparams[0m[0;90m => [0m[0;90m[[0m
    [0;32m'plugin'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'controller'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'action'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'_ext'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'pass'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mdata[0m[0;90m => [0m[0;90m[[0m
    [0;32m'_Token'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'fields'[0m[0;90m => [0m[0;32m'62e0f0cc58dc79c574b8d586cb2d68786d53ed8c%3A'[0m[0;90m,[0m
      [0;32m'unlocked'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
      [0;32m'debug'[0m[0;90m => [0m[0;32m'FormProtector debug data would be added here'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'_csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mquery[0m[0;90m => [0m[0;90m[[0m
    [0;32m'database'[0m[0;90m => [0m[0;32m'test_projects'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mcookies[0m[0;90m => [0m[0;90m[[0m
    [0;32m'csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_environment[0m[0;90m => [0m[0;90m[[0m
    [0;32m'PATH'[0m[0;90m => [0m[0;32m'/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin'[0m[0;90m,[0m
    [0;32m'HOSTNAME'[0m[0;90m => [0m[0;32m'8459499160cb'[0m[0;90m,[0m
    [0;32m'MYSQL_ROOT_PASSWORD'[0m[0;90m => [0m[0;32m'root'[0m[0;90m,[0m
    [0;32m'PHPIZE_DEPS'[0m[0;90m => [0m[0;32m'autoconf 		dpkg-dev 		file 		g++ 		gcc 		libc-dev 		make 		pkg-config 		re2c'[0m[0;90m,[0m
    [0;32m'PHP_INI_DIR'[0m[0;90m => [0m[0;32m'/usr/local/etc/php'[0m[0;90m,[0m
    [0;32m'APACHE_CONFDIR'[0m[0;90m => [0m[0;32m'/etc/apache2'[0m[0;90m,[0m
    [0;32m'APACHE_ENVVARS'[0m[0;90m => [0m[0;32m'/etc/apache2/envvars'[0m[0;90m,[0m
    [0;32m'PHP_CFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_CPPFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_LDFLAGS'[0m[0;90m => [0m[0;32m'-Wl,-O1 -pie'[0m[0;90m,[0m
    [0;32m'GPG_KEYS'[0m[0;90m => [0m[0;32m'42670A7FE4D0441C8E4632349E4FDC074A4EF02D 5A52880781F755608BF815FC910DEB46F53EA312'[0m[0;90m,[0m
    [0;32m'PHP_VERSION'[0m[0;90m => [0m[0;32m'7.4.25'[0m[0;90m,[0m
    [0;32m'PHP_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz'[0m[0;90m,[0m
    [0;32m'PHP_ASC_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz.asc'[0m[0;90m,[0m
    [0;32m'PHP_SHA256'[0m[0;90m => [0m[0;32m'12a758f1d7fee544387a28d3cf73226f47e3a52fb3049f07fcc37d156d393c0a'[0m[0;90m,[0m
    [0;32m'TZ'[0m[0;90m => [0m[0;32m'Europe/Berlin'[0m[0;90m,[0m
    [0;32m'IDE_PHPUNIT_CUSTOM_LOADER'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/autoload.php'[0m[0;90m,[0m
    [0;32m'JETBRAINS_REMOTE_RUN'[0m[0;90m => [0m[0;32m'1'[0m[0;90m,[0m
    [0;32m'TERM'[0m[0;90m => [0m[0;32m'xterm'[0m[0;90m,[0m
    [0;32m'HOME'[0m[0;90m => [0m[0;32m'/root'[0m[0;90m,[0m
    [0;32m'PHP_SELF'[0m[0;90m => [0m[0;32m'/'[0m[0;90m,[0m
    [0;32m'SCRIPT_NAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'SCRIPT_FILENAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'PATH_TRANSLATED'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'DOCUMENT_ROOT'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
    [0;32m'REQUEST_TIME_FLOAT'[0m[0;90m => [0m[0;35m(float)[0m [1;34m1637855853.9672[0m[0;90m,[0m
    [0;32m'REQUEST_TIME'[0m[0;90m => [0m[0;35m(int)[0m [1;34m1637855853[0m[0;90m,[0m
    [0;32m'argv'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'--configuration'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'/var/www/html/phpunit.xml.dist'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'--teamcity'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'argc'[0m[0;90m => [0m[0;35m(int)[0m [1;34m4[0m[0;90m,[0m
    [0;32m'REQUEST_URI'[0m[0;90m => [0m[0;32m'/wiki/index'[0m[0;90m,[0m
    [0;32m'REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m[0;90m,[0m
    [0;32m'QUERY_STRING'[0m[0;90m => [0m[0;32m'database=projects'[0m[0;90m,[0m
    [0;32m'HTTPS'[0m[0;90m => [0m[0;32m'on'[0m[0;90m,[0m
    [0;32m'ORIGINAL_REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mbase[0m[0;90m => [0m[0;32m''[0m
  [0;35mprotected[0m [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
  [0;35mprotected[0m [0;39mtrustedProxies[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39m_detectors[0m[0;90m => [0m[0;90m[[0m
    [0;32m'get'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'GET'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'post'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'POST'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'put'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PUT'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'patch'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PATCH'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'delete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'DELETE'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'head'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'HEAD'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'OPTIONS'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ssl'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTPS'[0m[0;90m,[0m
      [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ajax'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTP_X_REQUESTED_WITH'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'XMLHttpRequest'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'json'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'json'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'xml'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'xml'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'mobile'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'tablet'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'csv'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'csv'[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_detectorCache[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\PhpInputStream[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mresource[0m[0;90m => [0m(resource) Resource id #3107
    [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;32m'php://input'[0m
    [0;35mprivate[0m [0;39mcache[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mreachedEof[0m[0;90m => [0m[1;33mfalse[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39muri[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\Uri[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m
    [0;39mbase[0m[0;90m => [0m[0;32m''[0m
    [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
    [0;35mprotected[0m [0;39mallowedSchemes[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprivate[0m [0;39mscheme[0m[0;90m => [0m[0;32m'https'[0m
    [0;35mprivate[0m [0;39muserInfo[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mhost[0m[0;90m => [0m[0;32m'localhost'[0m
    [0;35mprivate[0m [0;39mport[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprivate[0m [0;39mpath[0m[0;90m => [0m[0;32m'/wiki/index'[0m
    [0;35mprivate[0m [0;39mquery[0m[0;90m => [0m[0;32m'database=projects'[0m
    [0;35mprivate[0m [0;39mfragment[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39muriString[0m[0;90m => [0m[1;33mnull[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39msession[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m
    [0;35mprotected[0m [0;39m_engine[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session\CacheSession[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_started[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_lifetime[0m[0;90m => [0m[0;35m(int)[0m [1;34m1440[0m
    [0;35mprotected[0m [0;39m_isCLI[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mattributes[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39memulatedAttributes[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'session'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'webroot'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'base'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'params'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;32m'here'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39muploadedFiles[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mprotocol[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39mrequestTarget[0m[0;90m => [0m[1;33mnull[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-14" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/Http/Runner.php&amp;line=73">CORE/src/Http/Runner.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-14">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="69"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">queue</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">valid</span><span style="color: #007700">())&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="70"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$middleware&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">queue</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">current</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="71"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">queue</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">next</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="72"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="73"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$middleware</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">process</span><span style="color: #007700">(</span><span style="color: #0000BB">$request</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="74"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="75"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="76"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">fallbackHandler</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="77"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">fallbackHandler</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">handle</span><span style="color: #007700">(</span><span style="color: #0000BB">$request</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-14" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Http\ServerRequest[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;39mtrustProxy[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprotected[0m [0;39mparams[0m[0;90m => [0m[0;90m[[0m
    [0;32m'plugin'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'controller'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'action'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'_ext'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'pass'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mdata[0m[0;90m => [0m[0;90m[[0m
    [0;32m'_Token'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'fields'[0m[0;90m => [0m[0;32m'62e0f0cc58dc79c574b8d586cb2d68786d53ed8c%3A'[0m[0;90m,[0m
      [0;32m'unlocked'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
      [0;32m'debug'[0m[0;90m => [0m[0;32m'FormProtector debug data would be added here'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'_csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mquery[0m[0;90m => [0m[0;90m[[0m
    [0;32m'database'[0m[0;90m => [0m[0;32m'test_projects'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mcookies[0m[0;90m => [0m[0;90m[[0m
    [0;32m'csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_environment[0m[0;90m => [0m[0;90m[[0m
    [0;32m'PATH'[0m[0;90m => [0m[0;32m'/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin'[0m[0;90m,[0m
    [0;32m'HOSTNAME'[0m[0;90m => [0m[0;32m'8459499160cb'[0m[0;90m,[0m
    [0;32m'MYSQL_ROOT_PASSWORD'[0m[0;90m => [0m[0;32m'root'[0m[0;90m,[0m
    [0;32m'PHPIZE_DEPS'[0m[0;90m => [0m[0;32m'autoconf 		dpkg-dev 		file 		g++ 		gcc 		libc-dev 		make 		pkg-config 		re2c'[0m[0;90m,[0m
    [0;32m'PHP_INI_DIR'[0m[0;90m => [0m[0;32m'/usr/local/etc/php'[0m[0;90m,[0m
    [0;32m'APACHE_CONFDIR'[0m[0;90m => [0m[0;32m'/etc/apache2'[0m[0;90m,[0m
    [0;32m'APACHE_ENVVARS'[0m[0;90m => [0m[0;32m'/etc/apache2/envvars'[0m[0;90m,[0m
    [0;32m'PHP_CFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_CPPFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_LDFLAGS'[0m[0;90m => [0m[0;32m'-Wl,-O1 -pie'[0m[0;90m,[0m
    [0;32m'GPG_KEYS'[0m[0;90m => [0m[0;32m'42670A7FE4D0441C8E4632349E4FDC074A4EF02D 5A52880781F755608BF815FC910DEB46F53EA312'[0m[0;90m,[0m
    [0;32m'PHP_VERSION'[0m[0;90m => [0m[0;32m'7.4.25'[0m[0;90m,[0m
    [0;32m'PHP_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz'[0m[0;90m,[0m
    [0;32m'PHP_ASC_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz.asc'[0m[0;90m,[0m
    [0;32m'PHP_SHA256'[0m[0;90m => [0m[0;32m'12a758f1d7fee544387a28d3cf73226f47e3a52fb3049f07fcc37d156d393c0a'[0m[0;90m,[0m
    [0;32m'TZ'[0m[0;90m => [0m[0;32m'Europe/Berlin'[0m[0;90m,[0m
    [0;32m'IDE_PHPUNIT_CUSTOM_LOADER'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/autoload.php'[0m[0;90m,[0m
    [0;32m'JETBRAINS_REMOTE_RUN'[0m[0;90m => [0m[0;32m'1'[0m[0;90m,[0m
    [0;32m'TERM'[0m[0;90m => [0m[0;32m'xterm'[0m[0;90m,[0m
    [0;32m'HOME'[0m[0;90m => [0m[0;32m'/root'[0m[0;90m,[0m
    [0;32m'PHP_SELF'[0m[0;90m => [0m[0;32m'/'[0m[0;90m,[0m
    [0;32m'SCRIPT_NAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'SCRIPT_FILENAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'PATH_TRANSLATED'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'DOCUMENT_ROOT'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
    [0;32m'REQUEST_TIME_FLOAT'[0m[0;90m => [0m[0;35m(float)[0m [1;34m1637855853.9672[0m[0;90m,[0m
    [0;32m'REQUEST_TIME'[0m[0;90m => [0m[0;35m(int)[0m [1;34m1637855853[0m[0;90m,[0m
    [0;32m'argv'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'--configuration'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'/var/www/html/phpunit.xml.dist'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'--teamcity'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'argc'[0m[0;90m => [0m[0;35m(int)[0m [1;34m4[0m[0;90m,[0m
    [0;32m'REQUEST_URI'[0m[0;90m => [0m[0;32m'/wiki/index'[0m[0;90m,[0m
    [0;32m'REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m[0;90m,[0m
    [0;32m'QUERY_STRING'[0m[0;90m => [0m[0;32m'database=projects'[0m[0;90m,[0m
    [0;32m'HTTPS'[0m[0;90m => [0m[0;32m'on'[0m[0;90m,[0m
    [0;32m'ORIGINAL_REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mbase[0m[0;90m => [0m[0;32m''[0m
  [0;35mprotected[0m [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
  [0;35mprotected[0m [0;39mtrustedProxies[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39m_detectors[0m[0;90m => [0m[0;90m[[0m
    [0;32m'get'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'GET'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'post'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'POST'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'put'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PUT'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'patch'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PATCH'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'delete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'DELETE'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'head'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'HEAD'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'OPTIONS'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ssl'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTPS'[0m[0;90m,[0m
      [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ajax'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTP_X_REQUESTED_WITH'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'XMLHttpRequest'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'json'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'json'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'xml'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'xml'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'mobile'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'tablet'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'csv'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'csv'[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_detectorCache[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\PhpInputStream[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mresource[0m[0;90m => [0m(resource) Resource id #3107
    [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;32m'php://input'[0m
    [0;35mprivate[0m [0;39mcache[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mreachedEof[0m[0;90m => [0m[1;33mfalse[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39muri[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\Uri[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m
    [0;39mbase[0m[0;90m => [0m[0;32m''[0m
    [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
    [0;35mprotected[0m [0;39mallowedSchemes[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprivate[0m [0;39mscheme[0m[0;90m => [0m[0;32m'https'[0m
    [0;35mprivate[0m [0;39muserInfo[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mhost[0m[0;90m => [0m[0;32m'localhost'[0m
    [0;35mprivate[0m [0;39mport[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprivate[0m [0;39mpath[0m[0;90m => [0m[0;32m'/wiki/index'[0m
    [0;35mprivate[0m [0;39mquery[0m[0;90m => [0m[0;32m'database=projects'[0m
    [0;35mprivate[0m [0;39mfragment[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39muriString[0m[0;90m => [0m[1;33mnull[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39msession[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m
    [0;35mprotected[0m [0;39m_engine[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session\CacheSession[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_started[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_lifetime[0m[0;90m => [0m[0;35m(int)[0m [1;34m1440[0m
    [0;35mprotected[0m [0;39m_isCLI[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mattributes[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39memulatedAttributes[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'session'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'webroot'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'base'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'params'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;32m'here'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39muploadedFiles[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mprotocol[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39mrequestTarget[0m[0;90m => [0m[1;33mnull[0m
[0;90m}[0m</div>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Http\Runner[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;35mprotected[0m [0;39mqueue[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\MiddlewareQueue[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mposition[0m[0;90m => [0m[0;35m(int)[0m [1;34m5[0m
    [0;35mprotected[0m [0;39mqueue[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mfallbackHandler[0m[0;90m => [0m[0;90mobject([0m[0;36mApp\Application[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mconfigDir[0m[0;90m => [0m[0;32m'/var/www/html/config/'[0m
    [0;35mprotected[0m [0;39mplugins[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Core\PluginCollection[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39mcontrollerFactory[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ControllerFactory[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_eventManager[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Event\EventManager[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_eventClass[0m[0;90m => [0m[0;32m'Cake\Event\Event'[0m
  [0;90m}[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-15" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/Routing/Middleware/AssetMiddleware.php&amp;line=68">CORE/src/Routing/Middleware/AssetMiddleware.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-15">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="64"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">public&nbsp;function&nbsp;</span><span style="color: #0000BB">process</span><span style="color: #007700">(</span><span style="color: #0000BB">ServerRequestInterface&nbsp;$request</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">RequestHandlerInterface&nbsp;$handler</span><span style="color: #007700">):&nbsp;</span><span style="color: #0000BB">ResponseInterface</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="65"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="66"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$url&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$request</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">getUri</span><span style="color: #007700">()-&gt;</span><span style="color: #0000BB">getPath</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="67"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">strpos</span><span style="color: #007700">(</span><span style="color: #0000BB">$url</span><span style="color: #007700">,&nbsp;</span><span style="color: #DD0000">'..'</span><span style="color: #007700">)&nbsp;!==&nbsp;</span><span style="color: #0000BB">false&nbsp;</span><span style="color: #007700">||&nbsp;</span><span style="color: #0000BB">strpos</span><span style="color: #007700">(</span><span style="color: #0000BB">$url</span><span style="color: #007700">,&nbsp;</span><span style="color: #DD0000">'.'</span><span style="color: #007700">)&nbsp;===&nbsp;</span><span style="color: #0000BB">false</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="68"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$handler</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">handle</span><span style="color: #007700">(</span><span style="color: #0000BB">$request</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="69"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="70"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="71"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">strpos</span><span style="color: #007700">(</span><span style="color: #0000BB">$url</span><span style="color: #007700">,&nbsp;</span><span style="color: #DD0000">'/.'</span><span style="color: #007700">)&nbsp;!==&nbsp;</span><span style="color: #0000BB">false</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="72"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$handler</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">handle</span><span style="color: #007700">(</span><span style="color: #0000BB">$request</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-15" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Http\ServerRequest[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;39mtrustProxy[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprotected[0m [0;39mparams[0m[0;90m => [0m[0;90m[[0m
    [0;32m'plugin'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'controller'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'action'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'_ext'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'pass'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mdata[0m[0;90m => [0m[0;90m[[0m
    [0;32m'_Token'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'fields'[0m[0;90m => [0m[0;32m'62e0f0cc58dc79c574b8d586cb2d68786d53ed8c%3A'[0m[0;90m,[0m
      [0;32m'unlocked'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
      [0;32m'debug'[0m[0;90m => [0m[0;32m'FormProtector debug data would be added here'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'_csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mquery[0m[0;90m => [0m[0;90m[[0m
    [0;32m'database'[0m[0;90m => [0m[0;32m'test_projects'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mcookies[0m[0;90m => [0m[0;90m[[0m
    [0;32m'csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_environment[0m[0;90m => [0m[0;90m[[0m
    [0;32m'PATH'[0m[0;90m => [0m[0;32m'/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin'[0m[0;90m,[0m
    [0;32m'HOSTNAME'[0m[0;90m => [0m[0;32m'8459499160cb'[0m[0;90m,[0m
    [0;32m'MYSQL_ROOT_PASSWORD'[0m[0;90m => [0m[0;32m'root'[0m[0;90m,[0m
    [0;32m'PHPIZE_DEPS'[0m[0;90m => [0m[0;32m'autoconf 		dpkg-dev 		file 		g++ 		gcc 		libc-dev 		make 		pkg-config 		re2c'[0m[0;90m,[0m
    [0;32m'PHP_INI_DIR'[0m[0;90m => [0m[0;32m'/usr/local/etc/php'[0m[0;90m,[0m
    [0;32m'APACHE_CONFDIR'[0m[0;90m => [0m[0;32m'/etc/apache2'[0m[0;90m,[0m
    [0;32m'APACHE_ENVVARS'[0m[0;90m => [0m[0;32m'/etc/apache2/envvars'[0m[0;90m,[0m
    [0;32m'PHP_CFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_CPPFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_LDFLAGS'[0m[0;90m => [0m[0;32m'-Wl,-O1 -pie'[0m[0;90m,[0m
    [0;32m'GPG_KEYS'[0m[0;90m => [0m[0;32m'42670A7FE4D0441C8E4632349E4FDC074A4EF02D 5A52880781F755608BF815FC910DEB46F53EA312'[0m[0;90m,[0m
    [0;32m'PHP_VERSION'[0m[0;90m => [0m[0;32m'7.4.25'[0m[0;90m,[0m
    [0;32m'PHP_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz'[0m[0;90m,[0m
    [0;32m'PHP_ASC_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz.asc'[0m[0;90m,[0m
    [0;32m'PHP_SHA256'[0m[0;90m => [0m[0;32m'12a758f1d7fee544387a28d3cf73226f47e3a52fb3049f07fcc37d156d393c0a'[0m[0;90m,[0m
    [0;32m'TZ'[0m[0;90m => [0m[0;32m'Europe/Berlin'[0m[0;90m,[0m
    [0;32m'IDE_PHPUNIT_CUSTOM_LOADER'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/autoload.php'[0m[0;90m,[0m
    [0;32m'JETBRAINS_REMOTE_RUN'[0m[0;90m => [0m[0;32m'1'[0m[0;90m,[0m
    [0;32m'TERM'[0m[0;90m => [0m[0;32m'xterm'[0m[0;90m,[0m
    [0;32m'HOME'[0m[0;90m => [0m[0;32m'/root'[0m[0;90m,[0m
    [0;32m'PHP_SELF'[0m[0;90m => [0m[0;32m'/'[0m[0;90m,[0m
    [0;32m'SCRIPT_NAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'SCRIPT_FILENAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'PATH_TRANSLATED'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'DOCUMENT_ROOT'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
    [0;32m'REQUEST_TIME_FLOAT'[0m[0;90m => [0m[0;35m(float)[0m [1;34m1637855853.9672[0m[0;90m,[0m
    [0;32m'REQUEST_TIME'[0m[0;90m => [0m[0;35m(int)[0m [1;34m1637855853[0m[0;90m,[0m
    [0;32m'argv'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'--configuration'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'/var/www/html/phpunit.xml.dist'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'--teamcity'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'argc'[0m[0;90m => [0m[0;35m(int)[0m [1;34m4[0m[0;90m,[0m
    [0;32m'REQUEST_URI'[0m[0;90m => [0m[0;32m'/wiki/index'[0m[0;90m,[0m
    [0;32m'REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m[0;90m,[0m
    [0;32m'QUERY_STRING'[0m[0;90m => [0m[0;32m'database=projects'[0m[0;90m,[0m
    [0;32m'HTTPS'[0m[0;90m => [0m[0;32m'on'[0m[0;90m,[0m
    [0;32m'ORIGINAL_REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mbase[0m[0;90m => [0m[0;32m''[0m
  [0;35mprotected[0m [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
  [0;35mprotected[0m [0;39mtrustedProxies[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39m_detectors[0m[0;90m => [0m[0;90m[[0m
    [0;32m'get'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'GET'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'post'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'POST'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'put'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PUT'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'patch'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PATCH'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'delete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'DELETE'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'head'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'HEAD'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'OPTIONS'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ssl'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTPS'[0m[0;90m,[0m
      [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ajax'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTP_X_REQUESTED_WITH'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'XMLHttpRequest'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'json'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'json'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'xml'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'xml'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'mobile'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'tablet'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'csv'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'csv'[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_detectorCache[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\PhpInputStream[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mresource[0m[0;90m => [0m(resource) Resource id #3107
    [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;32m'php://input'[0m
    [0;35mprivate[0m [0;39mcache[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mreachedEof[0m[0;90m => [0m[1;33mfalse[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39muri[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\Uri[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m
    [0;39mbase[0m[0;90m => [0m[0;32m''[0m
    [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
    [0;35mprotected[0m [0;39mallowedSchemes[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprivate[0m [0;39mscheme[0m[0;90m => [0m[0;32m'https'[0m
    [0;35mprivate[0m [0;39muserInfo[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mhost[0m[0;90m => [0m[0;32m'localhost'[0m
    [0;35mprivate[0m [0;39mport[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprivate[0m [0;39mpath[0m[0;90m => [0m[0;32m'/wiki/index'[0m
    [0;35mprivate[0m [0;39mquery[0m[0;90m => [0m[0;32m'database=projects'[0m
    [0;35mprivate[0m [0;39mfragment[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39muriString[0m[0;90m => [0m[1;33mnull[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39msession[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m
    [0;35mprotected[0m [0;39m_engine[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session\CacheSession[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_started[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_lifetime[0m[0;90m => [0m[0;35m(int)[0m [1;34m1440[0m
    [0;35mprotected[0m [0;39m_isCLI[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mattributes[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39memulatedAttributes[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'session'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'webroot'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'base'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'params'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;32m'here'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39muploadedFiles[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mprotocol[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39mrequestTarget[0m[0;90m => [0m[1;33mnull[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-16" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/Http/Runner.php&amp;line=73">CORE/src/Http/Runner.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-16">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="69"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">queue</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">valid</span><span style="color: #007700">())&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="70"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$middleware&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">queue</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">current</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="71"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">queue</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">next</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="72"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="73"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$middleware</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">process</span><span style="color: #007700">(</span><span style="color: #0000BB">$request</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="74"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="75"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="76"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">fallbackHandler</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="77"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">fallbackHandler</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">handle</span><span style="color: #007700">(</span><span style="color: #0000BB">$request</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-16" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Http\ServerRequest[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;39mtrustProxy[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprotected[0m [0;39mparams[0m[0;90m => [0m[0;90m[[0m
    [0;32m'plugin'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'controller'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'action'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'_ext'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'pass'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mdata[0m[0;90m => [0m[0;90m[[0m
    [0;32m'_Token'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'fields'[0m[0;90m => [0m[0;32m'62e0f0cc58dc79c574b8d586cb2d68786d53ed8c%3A'[0m[0;90m,[0m
      [0;32m'unlocked'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
      [0;32m'debug'[0m[0;90m => [0m[0;32m'FormProtector debug data would be added here'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'_csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mquery[0m[0;90m => [0m[0;90m[[0m
    [0;32m'database'[0m[0;90m => [0m[0;32m'test_projects'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mcookies[0m[0;90m => [0m[0;90m[[0m
    [0;32m'csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_environment[0m[0;90m => [0m[0;90m[[0m
    [0;32m'PATH'[0m[0;90m => [0m[0;32m'/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin'[0m[0;90m,[0m
    [0;32m'HOSTNAME'[0m[0;90m => [0m[0;32m'8459499160cb'[0m[0;90m,[0m
    [0;32m'MYSQL_ROOT_PASSWORD'[0m[0;90m => [0m[0;32m'root'[0m[0;90m,[0m
    [0;32m'PHPIZE_DEPS'[0m[0;90m => [0m[0;32m'autoconf 		dpkg-dev 		file 		g++ 		gcc 		libc-dev 		make 		pkg-config 		re2c'[0m[0;90m,[0m
    [0;32m'PHP_INI_DIR'[0m[0;90m => [0m[0;32m'/usr/local/etc/php'[0m[0;90m,[0m
    [0;32m'APACHE_CONFDIR'[0m[0;90m => [0m[0;32m'/etc/apache2'[0m[0;90m,[0m
    [0;32m'APACHE_ENVVARS'[0m[0;90m => [0m[0;32m'/etc/apache2/envvars'[0m[0;90m,[0m
    [0;32m'PHP_CFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_CPPFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_LDFLAGS'[0m[0;90m => [0m[0;32m'-Wl,-O1 -pie'[0m[0;90m,[0m
    [0;32m'GPG_KEYS'[0m[0;90m => [0m[0;32m'42670A7FE4D0441C8E4632349E4FDC074A4EF02D 5A52880781F755608BF815FC910DEB46F53EA312'[0m[0;90m,[0m
    [0;32m'PHP_VERSION'[0m[0;90m => [0m[0;32m'7.4.25'[0m[0;90m,[0m
    [0;32m'PHP_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz'[0m[0;90m,[0m
    [0;32m'PHP_ASC_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz.asc'[0m[0;90m,[0m
    [0;32m'PHP_SHA256'[0m[0;90m => [0m[0;32m'12a758f1d7fee544387a28d3cf73226f47e3a52fb3049f07fcc37d156d393c0a'[0m[0;90m,[0m
    [0;32m'TZ'[0m[0;90m => [0m[0;32m'Europe/Berlin'[0m[0;90m,[0m
    [0;32m'IDE_PHPUNIT_CUSTOM_LOADER'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/autoload.php'[0m[0;90m,[0m
    [0;32m'JETBRAINS_REMOTE_RUN'[0m[0;90m => [0m[0;32m'1'[0m[0;90m,[0m
    [0;32m'TERM'[0m[0;90m => [0m[0;32m'xterm'[0m[0;90m,[0m
    [0;32m'HOME'[0m[0;90m => [0m[0;32m'/root'[0m[0;90m,[0m
    [0;32m'PHP_SELF'[0m[0;90m => [0m[0;32m'/'[0m[0;90m,[0m
    [0;32m'SCRIPT_NAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'SCRIPT_FILENAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'PATH_TRANSLATED'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'DOCUMENT_ROOT'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
    [0;32m'REQUEST_TIME_FLOAT'[0m[0;90m => [0m[0;35m(float)[0m [1;34m1637855853.9672[0m[0;90m,[0m
    [0;32m'REQUEST_TIME'[0m[0;90m => [0m[0;35m(int)[0m [1;34m1637855853[0m[0;90m,[0m
    [0;32m'argv'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'--configuration'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'/var/www/html/phpunit.xml.dist'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'--teamcity'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'argc'[0m[0;90m => [0m[0;35m(int)[0m [1;34m4[0m[0;90m,[0m
    [0;32m'REQUEST_URI'[0m[0;90m => [0m[0;32m'/wiki/index'[0m[0;90m,[0m
    [0;32m'REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m[0;90m,[0m
    [0;32m'QUERY_STRING'[0m[0;90m => [0m[0;32m'database=projects'[0m[0;90m,[0m
    [0;32m'HTTPS'[0m[0;90m => [0m[0;32m'on'[0m[0;90m,[0m
    [0;32m'ORIGINAL_REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mbase[0m[0;90m => [0m[0;32m''[0m
  [0;35mprotected[0m [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
  [0;35mprotected[0m [0;39mtrustedProxies[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39m_detectors[0m[0;90m => [0m[0;90m[[0m
    [0;32m'get'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'GET'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'post'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'POST'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'put'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PUT'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'patch'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PATCH'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'delete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'DELETE'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'head'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'HEAD'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'OPTIONS'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ssl'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTPS'[0m[0;90m,[0m
      [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ajax'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTP_X_REQUESTED_WITH'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'XMLHttpRequest'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'json'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'json'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'xml'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'xml'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'mobile'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'tablet'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'csv'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'csv'[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_detectorCache[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\PhpInputStream[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mresource[0m[0;90m => [0m(resource) Resource id #3107
    [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;32m'php://input'[0m
    [0;35mprivate[0m [0;39mcache[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mreachedEof[0m[0;90m => [0m[1;33mfalse[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39muri[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\Uri[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m
    [0;39mbase[0m[0;90m => [0m[0;32m''[0m
    [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
    [0;35mprotected[0m [0;39mallowedSchemes[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprivate[0m [0;39mscheme[0m[0;90m => [0m[0;32m'https'[0m
    [0;35mprivate[0m [0;39muserInfo[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mhost[0m[0;90m => [0m[0;32m'localhost'[0m
    [0;35mprivate[0m [0;39mport[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprivate[0m [0;39mpath[0m[0;90m => [0m[0;32m'/wiki/index'[0m
    [0;35mprivate[0m [0;39mquery[0m[0;90m => [0m[0;32m'database=projects'[0m
    [0;35mprivate[0m [0;39mfragment[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39muriString[0m[0;90m => [0m[1;33mnull[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39msession[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m
    [0;35mprotected[0m [0;39m_engine[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session\CacheSession[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_started[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_lifetime[0m[0;90m => [0m[0;35m(int)[0m [1;34m1440[0m
    [0;35mprotected[0m [0;39m_isCLI[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mattributes[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39memulatedAttributes[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'session'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'webroot'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'base'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'params'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;32m'here'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39muploadedFiles[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mprotocol[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39mrequestTarget[0m[0;90m => [0m[1;33mnull[0m
[0;90m}[0m</div>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Http\Runner[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;35mprotected[0m [0;39mqueue[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\MiddlewareQueue[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mposition[0m[0;90m => [0m[0;35m(int)[0m [1;34m5[0m
    [0;35mprotected[0m [0;39mqueue[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mfallbackHandler[0m[0;90m => [0m[0;90mobject([0m[0;36mApp\Application[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mconfigDir[0m[0;90m => [0m[0;32m'/var/www/html/config/'[0m
    [0;35mprotected[0m [0;39mplugins[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Core\PluginCollection[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39mcontrollerFactory[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ControllerFactory[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_eventManager[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Event\EventManager[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_eventClass[0m[0;90m => [0m[0;32m'Cake\Event\Event'[0m
  [0;90m}[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-17" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/Error/Middleware/ErrorHandlerMiddleware.php&amp;line=121">CORE/src/Error/Middleware/ErrorHandlerMiddleware.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-17">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="117"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">*/</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="118"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">public&nbsp;function&nbsp;</span><span style="color: #0000BB">process</span><span style="color: #007700">(</span><span style="color: #0000BB">ServerRequestInterface&nbsp;$request</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">RequestHandlerInterface&nbsp;$handler</span><span style="color: #007700">):&nbsp;</span><span style="color: #0000BB">ResponseInterface</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="119"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="120"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">try&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="121"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$handler</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">handle</span><span style="color: #007700">(</span><span style="color: #0000BB">$request</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="122"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}&nbsp;catch&nbsp;(</span><span style="color: #0000BB">RedirectException&nbsp;$exception</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="123"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">handleRedirect</span><span style="color: #007700">(</span><span style="color: #0000BB">$exception</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="124"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}&nbsp;catch&nbsp;(</span><span style="color: #0000BB">Throwable&nbsp;$exception</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="125"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">handleException</span><span style="color: #007700">(</span><span style="color: #0000BB">$exception</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">$request</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-17" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Http\ServerRequest[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;39mtrustProxy[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprotected[0m [0;39mparams[0m[0;90m => [0m[0;90m[[0m
    [0;32m'plugin'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'controller'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'action'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'_ext'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'pass'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mdata[0m[0;90m => [0m[0;90m[[0m
    [0;32m'_Token'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'fields'[0m[0;90m => [0m[0;32m'62e0f0cc58dc79c574b8d586cb2d68786d53ed8c%3A'[0m[0;90m,[0m
      [0;32m'unlocked'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
      [0;32m'debug'[0m[0;90m => [0m[0;32m'FormProtector debug data would be added here'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'_csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mquery[0m[0;90m => [0m[0;90m[[0m
    [0;32m'database'[0m[0;90m => [0m[0;32m'test_projects'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mcookies[0m[0;90m => [0m[0;90m[[0m
    [0;32m'csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_environment[0m[0;90m => [0m[0;90m[[0m
    [0;32m'PATH'[0m[0;90m => [0m[0;32m'/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin'[0m[0;90m,[0m
    [0;32m'HOSTNAME'[0m[0;90m => [0m[0;32m'8459499160cb'[0m[0;90m,[0m
    [0;32m'MYSQL_ROOT_PASSWORD'[0m[0;90m => [0m[0;32m'root'[0m[0;90m,[0m
    [0;32m'PHPIZE_DEPS'[0m[0;90m => [0m[0;32m'autoconf 		dpkg-dev 		file 		g++ 		gcc 		libc-dev 		make 		pkg-config 		re2c'[0m[0;90m,[0m
    [0;32m'PHP_INI_DIR'[0m[0;90m => [0m[0;32m'/usr/local/etc/php'[0m[0;90m,[0m
    [0;32m'APACHE_CONFDIR'[0m[0;90m => [0m[0;32m'/etc/apache2'[0m[0;90m,[0m
    [0;32m'APACHE_ENVVARS'[0m[0;90m => [0m[0;32m'/etc/apache2/envvars'[0m[0;90m,[0m
    [0;32m'PHP_CFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_CPPFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_LDFLAGS'[0m[0;90m => [0m[0;32m'-Wl,-O1 -pie'[0m[0;90m,[0m
    [0;32m'GPG_KEYS'[0m[0;90m => [0m[0;32m'42670A7FE4D0441C8E4632349E4FDC074A4EF02D 5A52880781F755608BF815FC910DEB46F53EA312'[0m[0;90m,[0m
    [0;32m'PHP_VERSION'[0m[0;90m => [0m[0;32m'7.4.25'[0m[0;90m,[0m
    [0;32m'PHP_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz'[0m[0;90m,[0m
    [0;32m'PHP_ASC_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz.asc'[0m[0;90m,[0m
    [0;32m'PHP_SHA256'[0m[0;90m => [0m[0;32m'12a758f1d7fee544387a28d3cf73226f47e3a52fb3049f07fcc37d156d393c0a'[0m[0;90m,[0m
    [0;32m'TZ'[0m[0;90m => [0m[0;32m'Europe/Berlin'[0m[0;90m,[0m
    [0;32m'IDE_PHPUNIT_CUSTOM_LOADER'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/autoload.php'[0m[0;90m,[0m
    [0;32m'JETBRAINS_REMOTE_RUN'[0m[0;90m => [0m[0;32m'1'[0m[0;90m,[0m
    [0;32m'TERM'[0m[0;90m => [0m[0;32m'xterm'[0m[0;90m,[0m
    [0;32m'HOME'[0m[0;90m => [0m[0;32m'/root'[0m[0;90m,[0m
    [0;32m'PHP_SELF'[0m[0;90m => [0m[0;32m'/'[0m[0;90m,[0m
    [0;32m'SCRIPT_NAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'SCRIPT_FILENAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'PATH_TRANSLATED'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'DOCUMENT_ROOT'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
    [0;32m'REQUEST_TIME_FLOAT'[0m[0;90m => [0m[0;35m(float)[0m [1;34m1637855853.9672[0m[0;90m,[0m
    [0;32m'REQUEST_TIME'[0m[0;90m => [0m[0;35m(int)[0m [1;34m1637855853[0m[0;90m,[0m
    [0;32m'argv'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'--configuration'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'/var/www/html/phpunit.xml.dist'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'--teamcity'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'argc'[0m[0;90m => [0m[0;35m(int)[0m [1;34m4[0m[0;90m,[0m
    [0;32m'REQUEST_URI'[0m[0;90m => [0m[0;32m'/wiki/index'[0m[0;90m,[0m
    [0;32m'REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m[0;90m,[0m
    [0;32m'QUERY_STRING'[0m[0;90m => [0m[0;32m'database=projects'[0m[0;90m,[0m
    [0;32m'HTTPS'[0m[0;90m => [0m[0;32m'on'[0m[0;90m,[0m
    [0;32m'ORIGINAL_REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mbase[0m[0;90m => [0m[0;32m''[0m
  [0;35mprotected[0m [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
  [0;35mprotected[0m [0;39mtrustedProxies[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39m_detectors[0m[0;90m => [0m[0;90m[[0m
    [0;32m'get'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'GET'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'post'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'POST'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'put'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PUT'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'patch'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PATCH'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'delete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'DELETE'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'head'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'HEAD'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'OPTIONS'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ssl'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTPS'[0m[0;90m,[0m
      [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ajax'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTP_X_REQUESTED_WITH'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'XMLHttpRequest'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'json'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'json'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'xml'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'xml'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'mobile'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'tablet'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'csv'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'csv'[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_detectorCache[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\PhpInputStream[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mresource[0m[0;90m => [0m(resource) Resource id #3107
    [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;32m'php://input'[0m
    [0;35mprivate[0m [0;39mcache[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mreachedEof[0m[0;90m => [0m[1;33mfalse[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39muri[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\Uri[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m
    [0;39mbase[0m[0;90m => [0m[0;32m''[0m
    [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
    [0;35mprotected[0m [0;39mallowedSchemes[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprivate[0m [0;39mscheme[0m[0;90m => [0m[0;32m'https'[0m
    [0;35mprivate[0m [0;39muserInfo[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mhost[0m[0;90m => [0m[0;32m'localhost'[0m
    [0;35mprivate[0m [0;39mport[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprivate[0m [0;39mpath[0m[0;90m => [0m[0;32m'/wiki/index'[0m
    [0;35mprivate[0m [0;39mquery[0m[0;90m => [0m[0;32m'database=projects'[0m
    [0;35mprivate[0m [0;39mfragment[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39muriString[0m[0;90m => [0m[1;33mnull[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39msession[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m
    [0;35mprotected[0m [0;39m_engine[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session\CacheSession[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_started[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_lifetime[0m[0;90m => [0m[0;35m(int)[0m [1;34m1440[0m
    [0;35mprotected[0m [0;39m_isCLI[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mattributes[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39memulatedAttributes[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'session'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'webroot'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'base'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'params'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;32m'here'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39muploadedFiles[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mprotocol[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39mrequestTarget[0m[0;90m => [0m[1;33mnull[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-18" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/Http/Runner.php&amp;line=73">CORE/src/Http/Runner.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-18">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="69"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">queue</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">valid</span><span style="color: #007700">())&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="70"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$middleware&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">queue</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">current</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="71"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">queue</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">next</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="72"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="73"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$middleware</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">process</span><span style="color: #007700">(</span><span style="color: #0000BB">$request</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="74"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="75"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="76"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">fallbackHandler</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="77"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">fallbackHandler</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">handle</span><span style="color: #007700">(</span><span style="color: #0000BB">$request</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-18" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Http\ServerRequest[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;39mtrustProxy[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprotected[0m [0;39mparams[0m[0;90m => [0m[0;90m[[0m
    [0;32m'plugin'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'controller'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'action'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'_ext'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'pass'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mdata[0m[0;90m => [0m[0;90m[[0m
    [0;32m'_Token'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'fields'[0m[0;90m => [0m[0;32m'62e0f0cc58dc79c574b8d586cb2d68786d53ed8c%3A'[0m[0;90m,[0m
      [0;32m'unlocked'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
      [0;32m'debug'[0m[0;90m => [0m[0;32m'FormProtector debug data would be added here'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'_csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mquery[0m[0;90m => [0m[0;90m[[0m
    [0;32m'database'[0m[0;90m => [0m[0;32m'test_projects'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mcookies[0m[0;90m => [0m[0;90m[[0m
    [0;32m'csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_environment[0m[0;90m => [0m[0;90m[[0m
    [0;32m'PATH'[0m[0;90m => [0m[0;32m'/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin'[0m[0;90m,[0m
    [0;32m'HOSTNAME'[0m[0;90m => [0m[0;32m'8459499160cb'[0m[0;90m,[0m
    [0;32m'MYSQL_ROOT_PASSWORD'[0m[0;90m => [0m[0;32m'root'[0m[0;90m,[0m
    [0;32m'PHPIZE_DEPS'[0m[0;90m => [0m[0;32m'autoconf 		dpkg-dev 		file 		g++ 		gcc 		libc-dev 		make 		pkg-config 		re2c'[0m[0;90m,[0m
    [0;32m'PHP_INI_DIR'[0m[0;90m => [0m[0;32m'/usr/local/etc/php'[0m[0;90m,[0m
    [0;32m'APACHE_CONFDIR'[0m[0;90m => [0m[0;32m'/etc/apache2'[0m[0;90m,[0m
    [0;32m'APACHE_ENVVARS'[0m[0;90m => [0m[0;32m'/etc/apache2/envvars'[0m[0;90m,[0m
    [0;32m'PHP_CFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_CPPFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_LDFLAGS'[0m[0;90m => [0m[0;32m'-Wl,-O1 -pie'[0m[0;90m,[0m
    [0;32m'GPG_KEYS'[0m[0;90m => [0m[0;32m'42670A7FE4D0441C8E4632349E4FDC074A4EF02D 5A52880781F755608BF815FC910DEB46F53EA312'[0m[0;90m,[0m
    [0;32m'PHP_VERSION'[0m[0;90m => [0m[0;32m'7.4.25'[0m[0;90m,[0m
    [0;32m'PHP_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz'[0m[0;90m,[0m
    [0;32m'PHP_ASC_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz.asc'[0m[0;90m,[0m
    [0;32m'PHP_SHA256'[0m[0;90m => [0m[0;32m'12a758f1d7fee544387a28d3cf73226f47e3a52fb3049f07fcc37d156d393c0a'[0m[0;90m,[0m
    [0;32m'TZ'[0m[0;90m => [0m[0;32m'Europe/Berlin'[0m[0;90m,[0m
    [0;32m'IDE_PHPUNIT_CUSTOM_LOADER'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/autoload.php'[0m[0;90m,[0m
    [0;32m'JETBRAINS_REMOTE_RUN'[0m[0;90m => [0m[0;32m'1'[0m[0;90m,[0m
    [0;32m'TERM'[0m[0;90m => [0m[0;32m'xterm'[0m[0;90m,[0m
    [0;32m'HOME'[0m[0;90m => [0m[0;32m'/root'[0m[0;90m,[0m
    [0;32m'PHP_SELF'[0m[0;90m => [0m[0;32m'/'[0m[0;90m,[0m
    [0;32m'SCRIPT_NAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'SCRIPT_FILENAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'PATH_TRANSLATED'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'DOCUMENT_ROOT'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
    [0;32m'REQUEST_TIME_FLOAT'[0m[0;90m => [0m[0;35m(float)[0m [1;34m1637855853.9672[0m[0;90m,[0m
    [0;32m'REQUEST_TIME'[0m[0;90m => [0m[0;35m(int)[0m [1;34m1637855853[0m[0;90m,[0m
    [0;32m'argv'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'--configuration'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'/var/www/html/phpunit.xml.dist'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'--teamcity'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'argc'[0m[0;90m => [0m[0;35m(int)[0m [1;34m4[0m[0;90m,[0m
    [0;32m'REQUEST_URI'[0m[0;90m => [0m[0;32m'/wiki/index'[0m[0;90m,[0m
    [0;32m'REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m[0;90m,[0m
    [0;32m'QUERY_STRING'[0m[0;90m => [0m[0;32m'database=projects'[0m[0;90m,[0m
    [0;32m'HTTPS'[0m[0;90m => [0m[0;32m'on'[0m[0;90m,[0m
    [0;32m'ORIGINAL_REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mbase[0m[0;90m => [0m[0;32m''[0m
  [0;35mprotected[0m [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
  [0;35mprotected[0m [0;39mtrustedProxies[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39m_detectors[0m[0;90m => [0m[0;90m[[0m
    [0;32m'get'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'GET'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'post'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'POST'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'put'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PUT'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'patch'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PATCH'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'delete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'DELETE'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'head'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'HEAD'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'OPTIONS'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ssl'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTPS'[0m[0;90m,[0m
      [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ajax'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTP_X_REQUESTED_WITH'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'XMLHttpRequest'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'json'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'json'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'xml'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'xml'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'mobile'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'tablet'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'csv'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'csv'[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_detectorCache[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\PhpInputStream[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mresource[0m[0;90m => [0m(resource) Resource id #3107
    [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;32m'php://input'[0m
    [0;35mprivate[0m [0;39mcache[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mreachedEof[0m[0;90m => [0m[1;33mfalse[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39muri[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\Uri[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m
    [0;39mbase[0m[0;90m => [0m[0;32m''[0m
    [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
    [0;35mprotected[0m [0;39mallowedSchemes[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprivate[0m [0;39mscheme[0m[0;90m => [0m[0;32m'https'[0m
    [0;35mprivate[0m [0;39muserInfo[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mhost[0m[0;90m => [0m[0;32m'localhost'[0m
    [0;35mprivate[0m [0;39mport[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprivate[0m [0;39mpath[0m[0;90m => [0m[0;32m'/wiki/index'[0m
    [0;35mprivate[0m [0;39mquery[0m[0;90m => [0m[0;32m'database=projects'[0m
    [0;35mprivate[0m [0;39mfragment[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39muriString[0m[0;90m => [0m[1;33mnull[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39msession[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m
    [0;35mprotected[0m [0;39m_engine[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session\CacheSession[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_started[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_lifetime[0m[0;90m => [0m[0;35m(int)[0m [1;34m1440[0m
    [0;35mprotected[0m [0;39m_isCLI[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mattributes[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39memulatedAttributes[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'session'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'webroot'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'base'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'params'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;32m'here'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39muploadedFiles[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mprotocol[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39mrequestTarget[0m[0;90m => [0m[1;33mnull[0m
[0;90m}[0m</div>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Http\Runner[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;35mprotected[0m [0;39mqueue[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\MiddlewareQueue[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mposition[0m[0;90m => [0m[0;35m(int)[0m [1;34m5[0m
    [0;35mprotected[0m [0;39mqueue[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mfallbackHandler[0m[0;90m => [0m[0;90mobject([0m[0;36mApp\Application[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mconfigDir[0m[0;90m => [0m[0;32m'/var/www/html/config/'[0m
    [0;35mprotected[0m [0;39mplugins[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Core\PluginCollection[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39mcontrollerFactory[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ControllerFactory[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_eventManager[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Event\EventManager[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_eventClass[0m[0;90m => [0m[0;32m'Cake\Event\Event'[0m
  [0;90m}[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-19" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/Http/Runner.php&amp;line=58">CORE/src/Http/Runner.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-19">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="54"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">queue&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$queue</span><span style="color: #007700">;</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="55"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">queue</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">rewind</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="56"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">fallbackHandler&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$fallbackHandler</span><span style="color: #007700">;</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="57"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="58"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">handle</span><span style="color: #007700">(</span><span style="color: #0000BB">$request</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="59"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="60"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="61"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #FF8000">/**</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="62"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">*&nbsp;</span><span style="color: #0000BB">Handle&nbsp;incoming&nbsp;server&nbsp;request&nbsp;</span><span style="color: #007700">and&nbsp;return&nbsp;</span><span style="color: #0000BB">a&nbsp;response</span><span style="color: #007700">.</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-19" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Http\ServerRequest[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;39mtrustProxy[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprotected[0m [0;39mparams[0m[0;90m => [0m[0;90m[[0m
    [0;32m'plugin'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'controller'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'action'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'_ext'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'pass'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mdata[0m[0;90m => [0m[0;90m[[0m
    [0;32m'_Token'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'fields'[0m[0;90m => [0m[0;32m'62e0f0cc58dc79c574b8d586cb2d68786d53ed8c%3A'[0m[0;90m,[0m
      [0;32m'unlocked'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
      [0;32m'debug'[0m[0;90m => [0m[0;32m'FormProtector debug data would be added here'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'_csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mquery[0m[0;90m => [0m[0;90m[[0m
    [0;32m'database'[0m[0;90m => [0m[0;32m'test_projects'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mcookies[0m[0;90m => [0m[0;90m[[0m
    [0;32m'csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_environment[0m[0;90m => [0m[0;90m[[0m
    [0;32m'PATH'[0m[0;90m => [0m[0;32m'/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin'[0m[0;90m,[0m
    [0;32m'HOSTNAME'[0m[0;90m => [0m[0;32m'8459499160cb'[0m[0;90m,[0m
    [0;32m'MYSQL_ROOT_PASSWORD'[0m[0;90m => [0m[0;32m'root'[0m[0;90m,[0m
    [0;32m'PHPIZE_DEPS'[0m[0;90m => [0m[0;32m'autoconf 		dpkg-dev 		file 		g++ 		gcc 		libc-dev 		make 		pkg-config 		re2c'[0m[0;90m,[0m
    [0;32m'PHP_INI_DIR'[0m[0;90m => [0m[0;32m'/usr/local/etc/php'[0m[0;90m,[0m
    [0;32m'APACHE_CONFDIR'[0m[0;90m => [0m[0;32m'/etc/apache2'[0m[0;90m,[0m
    [0;32m'APACHE_ENVVARS'[0m[0;90m => [0m[0;32m'/etc/apache2/envvars'[0m[0;90m,[0m
    [0;32m'PHP_CFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_CPPFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_LDFLAGS'[0m[0;90m => [0m[0;32m'-Wl,-O1 -pie'[0m[0;90m,[0m
    [0;32m'GPG_KEYS'[0m[0;90m => [0m[0;32m'42670A7FE4D0441C8E4632349E4FDC074A4EF02D 5A52880781F755608BF815FC910DEB46F53EA312'[0m[0;90m,[0m
    [0;32m'PHP_VERSION'[0m[0;90m => [0m[0;32m'7.4.25'[0m[0;90m,[0m
    [0;32m'PHP_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz'[0m[0;90m,[0m
    [0;32m'PHP_ASC_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz.asc'[0m[0;90m,[0m
    [0;32m'PHP_SHA256'[0m[0;90m => [0m[0;32m'12a758f1d7fee544387a28d3cf73226f47e3a52fb3049f07fcc37d156d393c0a'[0m[0;90m,[0m
    [0;32m'TZ'[0m[0;90m => [0m[0;32m'Europe/Berlin'[0m[0;90m,[0m
    [0;32m'IDE_PHPUNIT_CUSTOM_LOADER'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/autoload.php'[0m[0;90m,[0m
    [0;32m'JETBRAINS_REMOTE_RUN'[0m[0;90m => [0m[0;32m'1'[0m[0;90m,[0m
    [0;32m'TERM'[0m[0;90m => [0m[0;32m'xterm'[0m[0;90m,[0m
    [0;32m'HOME'[0m[0;90m => [0m[0;32m'/root'[0m[0;90m,[0m
    [0;32m'PHP_SELF'[0m[0;90m => [0m[0;32m'/'[0m[0;90m,[0m
    [0;32m'SCRIPT_NAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'SCRIPT_FILENAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'PATH_TRANSLATED'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'DOCUMENT_ROOT'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
    [0;32m'REQUEST_TIME_FLOAT'[0m[0;90m => [0m[0;35m(float)[0m [1;34m1637855853.9672[0m[0;90m,[0m
    [0;32m'REQUEST_TIME'[0m[0;90m => [0m[0;35m(int)[0m [1;34m1637855853[0m[0;90m,[0m
    [0;32m'argv'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'--configuration'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'/var/www/html/phpunit.xml.dist'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'--teamcity'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'argc'[0m[0;90m => [0m[0;35m(int)[0m [1;34m4[0m[0;90m,[0m
    [0;32m'REQUEST_URI'[0m[0;90m => [0m[0;32m'/wiki/index'[0m[0;90m,[0m
    [0;32m'REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m[0;90m,[0m
    [0;32m'QUERY_STRING'[0m[0;90m => [0m[0;32m'database=projects'[0m[0;90m,[0m
    [0;32m'HTTPS'[0m[0;90m => [0m[0;32m'on'[0m[0;90m,[0m
    [0;32m'ORIGINAL_REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mbase[0m[0;90m => [0m[0;32m''[0m
  [0;35mprotected[0m [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
  [0;35mprotected[0m [0;39mtrustedProxies[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39m_detectors[0m[0;90m => [0m[0;90m[[0m
    [0;32m'get'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'GET'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'post'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'POST'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'put'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PUT'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'patch'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PATCH'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'delete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'DELETE'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'head'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'HEAD'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'OPTIONS'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ssl'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTPS'[0m[0;90m,[0m
      [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ajax'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTP_X_REQUESTED_WITH'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'XMLHttpRequest'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'json'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'json'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'xml'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'xml'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'mobile'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'tablet'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'csv'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'csv'[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_detectorCache[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\PhpInputStream[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mresource[0m[0;90m => [0m(resource) Resource id #3107
    [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;32m'php://input'[0m
    [0;35mprivate[0m [0;39mcache[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mreachedEof[0m[0;90m => [0m[1;33mfalse[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39muri[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\Uri[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m
    [0;39mbase[0m[0;90m => [0m[0;32m''[0m
    [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
    [0;35mprotected[0m [0;39mallowedSchemes[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprivate[0m [0;39mscheme[0m[0;90m => [0m[0;32m'https'[0m
    [0;35mprivate[0m [0;39muserInfo[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mhost[0m[0;90m => [0m[0;32m'localhost'[0m
    [0;35mprivate[0m [0;39mport[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprivate[0m [0;39mpath[0m[0;90m => [0m[0;32m'/wiki/index'[0m
    [0;35mprivate[0m [0;39mquery[0m[0;90m => [0m[0;32m'database=projects'[0m
    [0;35mprivate[0m [0;39mfragment[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39muriString[0m[0;90m => [0m[1;33mnull[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39msession[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m
    [0;35mprotected[0m [0;39m_engine[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session\CacheSession[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_started[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_lifetime[0m[0;90m => [0m[0;35m(int)[0m [1;34m1440[0m
    [0;35mprotected[0m [0;39m_isCLI[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mattributes[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39memulatedAttributes[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'session'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'webroot'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'base'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'params'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;32m'here'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39muploadedFiles[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mprotocol[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39mrequestTarget[0m[0;90m => [0m[1;33mnull[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-20" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/Http/Server.php&amp;line=90">CORE/src/Http/Server.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-20">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="86"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="87"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="88"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">dispatchEvent</span><span style="color: #007700">(</span><span style="color: #DD0000">'Server.buildMiddleware'</span><span style="color: #007700">,&nbsp;[</span><span style="color: #DD0000">'middleware'&nbsp;</span><span style="color: #007700">=&gt;&nbsp;</span><span style="color: #0000BB">$middleware</span><span style="color: #007700">]);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="89"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="90"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$response&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">runner</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">run</span><span style="color: #007700">(</span><span style="color: #0000BB">$middleware</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">$request</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">app</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="91"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="92"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$request&nbsp;</span><span style="color: #007700">instanceof&nbsp;</span><span style="color: #0000BB">ServerRequest</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="93"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$request</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">getSession</span><span style="color: #007700">()-&gt;</span><span style="color: #0000BB">close</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="94"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-20" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Http\MiddlewareQueue[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;35mprotected[0m [0;39mposition[0m[0;90m => [0m[0;35m(int)[0m [1;34m5[0m
  [0;35mprotected[0m [0;39mqueue[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Error\Middleware\ErrorHandlerMiddleware[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Routing\Middleware\AssetMiddleware[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Middleware\HttpsEnforcerMiddleware[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Routing\Middleware\RoutingMiddleware[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Middleware\BodyParserMiddleware[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m[0;90m}[0m
  [0;90m][0m
[0;90m}[0m</div>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Http\ServerRequest[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;39mtrustProxy[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprotected[0m [0;39mparams[0m[0;90m => [0m[0;90m[[0m
    [0;32m'plugin'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'controller'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'action'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'_ext'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'pass'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mdata[0m[0;90m => [0m[0;90m[[0m
    [0;32m'_Token'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'fields'[0m[0;90m => [0m[0;32m'62e0f0cc58dc79c574b8d586cb2d68786d53ed8c%3A'[0m[0;90m,[0m
      [0;32m'unlocked'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
      [0;32m'debug'[0m[0;90m => [0m[0;32m'FormProtector debug data would be added here'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'_csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mquery[0m[0;90m => [0m[0;90m[[0m
    [0;32m'database'[0m[0;90m => [0m[0;32m'test_projects'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mcookies[0m[0;90m => [0m[0;90m[[0m
    [0;32m'csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_environment[0m[0;90m => [0m[0;90m[[0m
    [0;32m'PATH'[0m[0;90m => [0m[0;32m'/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin'[0m[0;90m,[0m
    [0;32m'HOSTNAME'[0m[0;90m => [0m[0;32m'8459499160cb'[0m[0;90m,[0m
    [0;32m'MYSQL_ROOT_PASSWORD'[0m[0;90m => [0m[0;32m'root'[0m[0;90m,[0m
    [0;32m'PHPIZE_DEPS'[0m[0;90m => [0m[0;32m'autoconf 		dpkg-dev 		file 		g++ 		gcc 		libc-dev 		make 		pkg-config 		re2c'[0m[0;90m,[0m
    [0;32m'PHP_INI_DIR'[0m[0;90m => [0m[0;32m'/usr/local/etc/php'[0m[0;90m,[0m
    [0;32m'APACHE_CONFDIR'[0m[0;90m => [0m[0;32m'/etc/apache2'[0m[0;90m,[0m
    [0;32m'APACHE_ENVVARS'[0m[0;90m => [0m[0;32m'/etc/apache2/envvars'[0m[0;90m,[0m
    [0;32m'PHP_CFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_CPPFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_LDFLAGS'[0m[0;90m => [0m[0;32m'-Wl,-O1 -pie'[0m[0;90m,[0m
    [0;32m'GPG_KEYS'[0m[0;90m => [0m[0;32m'42670A7FE4D0441C8E4632349E4FDC074A4EF02D 5A52880781F755608BF815FC910DEB46F53EA312'[0m[0;90m,[0m
    [0;32m'PHP_VERSION'[0m[0;90m => [0m[0;32m'7.4.25'[0m[0;90m,[0m
    [0;32m'PHP_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz'[0m[0;90m,[0m
    [0;32m'PHP_ASC_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz.asc'[0m[0;90m,[0m
    [0;32m'PHP_SHA256'[0m[0;90m => [0m[0;32m'12a758f1d7fee544387a28d3cf73226f47e3a52fb3049f07fcc37d156d393c0a'[0m[0;90m,[0m
    [0;32m'TZ'[0m[0;90m => [0m[0;32m'Europe/Berlin'[0m[0;90m,[0m
    [0;32m'IDE_PHPUNIT_CUSTOM_LOADER'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/autoload.php'[0m[0;90m,[0m
    [0;32m'JETBRAINS_REMOTE_RUN'[0m[0;90m => [0m[0;32m'1'[0m[0;90m,[0m
    [0;32m'TERM'[0m[0;90m => [0m[0;32m'xterm'[0m[0;90m,[0m
    [0;32m'HOME'[0m[0;90m => [0m[0;32m'/root'[0m[0;90m,[0m
    [0;32m'PHP_SELF'[0m[0;90m => [0m[0;32m'/'[0m[0;90m,[0m
    [0;32m'SCRIPT_NAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'SCRIPT_FILENAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'PATH_TRANSLATED'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'DOCUMENT_ROOT'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
    [0;32m'REQUEST_TIME_FLOAT'[0m[0;90m => [0m[0;35m(float)[0m [1;34m1637855853.9672[0m[0;90m,[0m
    [0;32m'REQUEST_TIME'[0m[0;90m => [0m[0;35m(int)[0m [1;34m1637855853[0m[0;90m,[0m
    [0;32m'argv'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'--configuration'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'/var/www/html/phpunit.xml.dist'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'--teamcity'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'argc'[0m[0;90m => [0m[0;35m(int)[0m [1;34m4[0m[0;90m,[0m
    [0;32m'REQUEST_URI'[0m[0;90m => [0m[0;32m'/wiki/index'[0m[0;90m,[0m
    [0;32m'REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m[0;90m,[0m
    [0;32m'QUERY_STRING'[0m[0;90m => [0m[0;32m'database=projects'[0m[0;90m,[0m
    [0;32m'HTTPS'[0m[0;90m => [0m[0;32m'on'[0m[0;90m,[0m
    [0;32m'ORIGINAL_REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mbase[0m[0;90m => [0m[0;32m''[0m
  [0;35mprotected[0m [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
  [0;35mprotected[0m [0;39mtrustedProxies[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39m_detectors[0m[0;90m => [0m[0;90m[[0m
    [0;32m'get'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'GET'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'post'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'POST'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'put'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PUT'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'patch'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PATCH'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'delete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'DELETE'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'head'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'HEAD'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'OPTIONS'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ssl'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTPS'[0m[0;90m,[0m
      [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ajax'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTP_X_REQUESTED_WITH'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'XMLHttpRequest'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'json'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'json'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'xml'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'xml'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'mobile'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'tablet'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'csv'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'csv'[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_detectorCache[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\PhpInputStream[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mresource[0m[0;90m => [0m(resource) Resource id #3107
    [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;32m'php://input'[0m
    [0;35mprivate[0m [0;39mcache[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mreachedEof[0m[0;90m => [0m[1;33mfalse[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39muri[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\Uri[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m
    [0;39mbase[0m[0;90m => [0m[0;32m''[0m
    [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
    [0;35mprotected[0m [0;39mallowedSchemes[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprivate[0m [0;39mscheme[0m[0;90m => [0m[0;32m'https'[0m
    [0;35mprivate[0m [0;39muserInfo[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mhost[0m[0;90m => [0m[0;32m'localhost'[0m
    [0;35mprivate[0m [0;39mport[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprivate[0m [0;39mpath[0m[0;90m => [0m[0;32m'/wiki/index'[0m
    [0;35mprivate[0m [0;39mquery[0m[0;90m => [0m[0;32m'database=projects'[0m
    [0;35mprivate[0m [0;39mfragment[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39muriString[0m[0;90m => [0m[1;33mnull[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39msession[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m
    [0;35mprotected[0m [0;39m_engine[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session\CacheSession[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_started[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_lifetime[0m[0;90m => [0m[0;35m(int)[0m [1;34m1440[0m
    [0;35mprotected[0m [0;39m_isCLI[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mattributes[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39memulatedAttributes[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'session'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'webroot'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'base'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'params'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;32m'here'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39muploadedFiles[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mprotocol[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39mrequestTarget[0m[0;90m => [0m[1;33mnull[0m
[0;90m}[0m</div>
                            <div class="cake-debug">[0;90mobject([0m[0;36mApp\Application[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;35mprotected[0m [0;39mconfigDir[0m[0;90m => [0m[0;32m'/var/www/html/config/'[0m
  [0;35mprotected[0m [0;39mplugins[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Core\PluginCollection[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mplugins[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mnames[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mpositions[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mloopDepth[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mcontrollerFactory[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ControllerFactory[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m
  [0;35mprotected[0m [0;39m_eventManager[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Event\EventManager[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m
    [0;39m'_listeners'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'Controller.initialize'[0m[0;90m => [0m[0;32m'2 listener(s)'[0m[0;90m,[0m
      [0;32m'TwigView.TwigView.construct'[0m[0;90m => [0m[0;32m'3 listener(s)'[0m
    [0;90m][0m
    [0;39m'_isGlobal'[0m[0;90m => [0m[1;33mtrue[0m
    [0;39m'_trackEvents'[0m[0;90m => [0m[1;33mfalse[0m
    [0;39m'_generalManager'[0m[0;90m => [0m[0;32m'(object) EventManager'[0m
    [0;39m'_dispatchedEvents'[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_generalManager[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Event\EventManager[0m[0;90m) id:[0m[1;34m3[0m[0;90m {}[0m
    [0;35mprotected[0m [0;39m_listeners[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_isGlobal[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_eventList[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_trackEvents[0m[0;90m => [0m[1;33mfalse[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39m_eventClass[0m[0;90m => [0m[0;32m'Cake\Event\Event'[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-21" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/TestSuite/MiddlewareDispatcher.php&amp;line=190">CORE/src/TestSuite/MiddlewareDispatcher.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-21">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="186"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="187"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="188"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$server&nbsp;</span><span style="color: #007700">=&nbsp;new&nbsp;</span><span style="color: #0000BB">Server</span><span style="color: #007700">(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">app</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="189"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="190"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;</span><span style="color: #0000BB">$server</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">run</span><span style="color: #007700">(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">_createRequest</span><span style="color: #007700">(</span><span style="color: #0000BB">$requestSpec</span><span style="color: #007700">));</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="191"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="192"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="193"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-21" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mCake\Http\ServerRequest[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;39mtrustProxy[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprotected[0m [0;39mparams[0m[0;90m => [0m[0;90m[[0m
    [0;32m'plugin'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'controller'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'action'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'_ext'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
    [0;32m'pass'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mdata[0m[0;90m => [0m[0;90m[[0m
    [0;32m'_Token'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'fields'[0m[0;90m => [0m[0;32m'62e0f0cc58dc79c574b8d586cb2d68786d53ed8c%3A'[0m[0;90m,[0m
      [0;32m'unlocked'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
      [0;32m'debug'[0m[0;90m => [0m[0;32m'FormProtector debug data would be added here'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'_csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mquery[0m[0;90m => [0m[0;90m[[0m
    [0;32m'database'[0m[0;90m => [0m[0;32m'test_projects'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mcookies[0m[0;90m => [0m[0;90m[[0m
    [0;32m'csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_environment[0m[0;90m => [0m[0;90m[[0m
    [0;32m'PATH'[0m[0;90m => [0m[0;32m'/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin'[0m[0;90m,[0m
    [0;32m'HOSTNAME'[0m[0;90m => [0m[0;32m'8459499160cb'[0m[0;90m,[0m
    [0;32m'MYSQL_ROOT_PASSWORD'[0m[0;90m => [0m[0;32m'root'[0m[0;90m,[0m
    [0;32m'PHPIZE_DEPS'[0m[0;90m => [0m[0;32m'autoconf 		dpkg-dev 		file 		g++ 		gcc 		libc-dev 		make 		pkg-config 		re2c'[0m[0;90m,[0m
    [0;32m'PHP_INI_DIR'[0m[0;90m => [0m[0;32m'/usr/local/etc/php'[0m[0;90m,[0m
    [0;32m'APACHE_CONFDIR'[0m[0;90m => [0m[0;32m'/etc/apache2'[0m[0;90m,[0m
    [0;32m'APACHE_ENVVARS'[0m[0;90m => [0m[0;32m'/etc/apache2/envvars'[0m[0;90m,[0m
    [0;32m'PHP_CFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_CPPFLAGS'[0m[0;90m => [0m[0;32m'-fstack-protector-strong -fpic -fpie -O2 -D_LARGEFILE_SOURCE -D_FILE_OFFSET_BITS=64'[0m[0;90m,[0m
    [0;32m'PHP_LDFLAGS'[0m[0;90m => [0m[0;32m'-Wl,-O1 -pie'[0m[0;90m,[0m
    [0;32m'GPG_KEYS'[0m[0;90m => [0m[0;32m'42670A7FE4D0441C8E4632349E4FDC074A4EF02D 5A52880781F755608BF815FC910DEB46F53EA312'[0m[0;90m,[0m
    [0;32m'PHP_VERSION'[0m[0;90m => [0m[0;32m'7.4.25'[0m[0;90m,[0m
    [0;32m'PHP_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz'[0m[0;90m,[0m
    [0;32m'PHP_ASC_URL'[0m[0;90m => [0m[0;32m'https://www.php.net/distributions/php-7.4.25.tar.xz.asc'[0m[0;90m,[0m
    [0;32m'PHP_SHA256'[0m[0;90m => [0m[0;32m'12a758f1d7fee544387a28d3cf73226f47e3a52fb3049f07fcc37d156d393c0a'[0m[0;90m,[0m
    [0;32m'TZ'[0m[0;90m => [0m[0;32m'Europe/Berlin'[0m[0;90m,[0m
    [0;32m'IDE_PHPUNIT_CUSTOM_LOADER'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/autoload.php'[0m[0;90m,[0m
    [0;32m'JETBRAINS_REMOTE_RUN'[0m[0;90m => [0m[0;32m'1'[0m[0;90m,[0m
    [0;32m'TERM'[0m[0;90m => [0m[0;32m'xterm'[0m[0;90m,[0m
    [0;32m'HOME'[0m[0;90m => [0m[0;32m'/root'[0m[0;90m,[0m
    [0;32m'PHP_SELF'[0m[0;90m => [0m[0;32m'/'[0m[0;90m,[0m
    [0;32m'SCRIPT_NAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'SCRIPT_FILENAME'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'PATH_TRANSLATED'[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
    [0;32m'DOCUMENT_ROOT'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
    [0;32m'REQUEST_TIME_FLOAT'[0m[0;90m => [0m[0;35m(float)[0m [1;34m1637855853.9672[0m[0;90m,[0m
    [0;32m'REQUEST_TIME'[0m[0;90m => [0m[0;35m(int)[0m [1;34m1637855853[0m[0;90m,[0m
    [0;32m'argv'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'--configuration'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'/var/www/html/phpunit.xml.dist'[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'--teamcity'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'argc'[0m[0;90m => [0m[0;35m(int)[0m [1;34m4[0m[0;90m,[0m
    [0;32m'REQUEST_URI'[0m[0;90m => [0m[0;32m'/wiki/index'[0m[0;90m,[0m
    [0;32m'REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m[0;90m,[0m
    [0;32m'QUERY_STRING'[0m[0;90m => [0m[0;32m'database=projects'[0m[0;90m,[0m
    [0;32m'HTTPS'[0m[0;90m => [0m[0;32m'on'[0m[0;90m,[0m
    [0;32m'ORIGINAL_REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mbase[0m[0;90m => [0m[0;32m''[0m
  [0;35mprotected[0m [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
  [0;35mprotected[0m [0;39mtrustedProxies[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39m_detectors[0m[0;90m => [0m[0;90m[[0m
    [0;32m'get'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'GET'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'post'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'POST'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'put'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PUT'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'patch'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'PATCH'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'delete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'DELETE'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'head'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'HEAD'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'REQUEST_METHOD'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'OPTIONS'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ssl'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTPS'[0m[0;90m,[0m
      [0;32m'options'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m
    [0;90m][0m[0;90m,[0m
    [0;32m'ajax'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'env'[0m[0;90m => [0m[0;32m'HTTP_X_REQUESTED_WITH'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'XMLHttpRequest'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'json'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'json'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'xml'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'xml'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'mobile'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'tablet'[0m[0;90m => [0m[0;90mobject([0m[0;36mClosure[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;32m'csv'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'accept'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'param'[0m[0;90m => [0m[0;32m'_ext'[0m[0;90m,[0m
      [0;32m'value'[0m[0;90m => [0m[0;32m'csv'[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_detectorCache[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\PhpInputStream[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mresource[0m[0;90m => [0m(resource) Resource id #3107
    [0;35mprotected[0m [0;39mstream[0m[0;90m => [0m[0;32m'php://input'[0m
    [0;35mprivate[0m [0;39mcache[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mreachedEof[0m[0;90m => [0m[1;33mfalse[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39muri[0m[0;90m => [0m[0;90mobject([0m[0;36mLaminas\Diactoros\Uri[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m
    [0;39mbase[0m[0;90m => [0m[0;32m''[0m
    [0;39mwebroot[0m[0;90m => [0m[0;32m'/'[0m
    [0;35mprotected[0m [0;39mallowedSchemes[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprivate[0m [0;39mscheme[0m[0;90m => [0m[0;32m'https'[0m
    [0;35mprivate[0m [0;39muserInfo[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39mhost[0m[0;90m => [0m[0;32m'localhost'[0m
    [0;35mprivate[0m [0;39mport[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprivate[0m [0;39mpath[0m[0;90m => [0m[0;32m'/wiki/index'[0m
    [0;35mprivate[0m [0;39mquery[0m[0;90m => [0m[0;32m'database=projects'[0m
    [0;35mprivate[0m [0;39mfragment[0m[0;90m => [0m[0;32m''[0m
    [0;35mprivate[0m [0;39muriString[0m[0;90m => [0m[1;33mnull[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39msession[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m
    [0;35mprotected[0m [0;39m_engine[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session\CacheSession[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_started[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_lifetime[0m[0;90m => [0m[0;35m(int)[0m [1;34m1440[0m
    [0;35mprotected[0m [0;39m_isCLI[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39mattributes[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39memulatedAttributes[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'session'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'webroot'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'base'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'params'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;32m'here'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39muploadedFiles[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mprotocol[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39mrequestTarget[0m[0;90m => [0m[1;33mnull[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-22" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/TestSuite/IntegrationTestTrait.php&amp;line=499">CORE/src/TestSuite/IntegrationTestTrait.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-22">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="495"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$url&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$dispatcher</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">resolveUrl</span><span style="color: #007700">(</span><span style="color: #0000BB">$url</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="496"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="497"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">try&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="498"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$request&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">_buildRequest</span><span style="color: #007700">(</span><span style="color: #0000BB">$url</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">$method</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">$data</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="499"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$response&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$dispatcher</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">execute</span><span style="color: #007700">(</span><span style="color: #0000BB">$request</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="500"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">_requestSession&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$request</span><span style="color: #007700">[</span><span style="color: #DD0000">'session'</span><span style="color: #007700">];</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="501"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">_retainFlashMessages&nbsp;</span><span style="color: #007700">&amp;&amp;&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">_flashMessages</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="502"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">_requestSession</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">write</span><span style="color: #007700">(</span><span style="color: #DD0000">'Flash'</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">_flashMessages</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="503"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-22" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90m[[0m
  [0;32m'url'[0m[0;90m => [0m[0;32m'/wiki/index'[0m[0;90m,[0m
  [0;32m'session'[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
    [0;35mprotected[0m [0;39m_engine[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Session\CacheSession[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_started[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_lifetime[0m[0;90m => [0m[0;35m(int)[0m [1;34m1440[0m
    [0;35mprotected[0m [0;39m_isCLI[0m[0;90m => [0m[1;33mtrue[0m
  [0;90m}[0m[0;90m,[0m
  [0;32m'query'[0m[0;90m => [0m[0;90m[[0m
    [0;32m'database'[0m[0;90m => [0m[0;32m'test_projects'[0m
  [0;90m][0m[0;90m,[0m
  [0;32m'files'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m[0;90m,[0m
  [0;32m'environment'[0m[0;90m => [0m[0;90m[[0m
    [0;32m'REQUEST_METHOD'[0m[0;90m => [0m[0;32m'GET'[0m[0;90m,[0m
    [0;32m'QUERY_STRING'[0m[0;90m => [0m[0;32m'database=projects'[0m[0;90m,[0m
    [0;32m'REQUEST_URI'[0m[0;90m => [0m[0;32m'/wiki/index'[0m[0;90m,[0m
    [0;32m'HTTPS'[0m[0;90m => [0m[0;32m'on'[0m
  [0;90m][0m[0;90m,[0m
  [0;32m'post'[0m[0;90m => [0m[0;90m[[0m
    [0;32m'_Token'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'fields'[0m[0;90m => [0m[0;32m'62e0f0cc58dc79c574b8d586cb2d68786d53ed8c%3A'[0m[0;90m,[0m
      [0;32m'unlocked'[0m[0;90m => [0m[0;32m''[0m[0;90m,[0m
      [0;32m'debug'[0m[0;90m => [0m[0;32m'FormProtector debug data would be added here'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'_csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m[0;90m,[0m
  [0;32m'cookies'[0m[0;90m => [0m[0;90m[[0m
    [0;32m'csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
[0;90m][0m</div>
                    </div>
    </div>
    <div id="stack-frame-23" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/cakephp/cakephp/src/TestSuite/IntegrationTestTrait.php&amp;line=385">CORE/src/TestSuite/IntegrationTestTrait.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-23">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="381"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">*&nbsp;@return&nbsp;</span><span style="color: #0000BB">void</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="382"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">*/</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="383"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">public&nbsp;function&nbsp;</span><span style="color: #0000BB">get</span><span style="color: #007700">(</span><span style="color: #0000BB">$url</span><span style="color: #007700">):&nbsp;</span><span style="color: #0000BB">void</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="384"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="385"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">_sendRequest</span><span style="color: #007700">(</span><span style="color: #0000BB">$url</span><span style="color: #007700">,&nbsp;</span><span style="color: #DD0000">'GET'</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="386"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="387"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="388"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #FF8000">/**</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="389"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">*&nbsp;</span><span style="color: #0000BB">Performs&nbsp;a&nbsp;POST&nbsp;request&nbsp;using&nbsp;the&nbsp;current&nbsp;request&nbsp;data</span><span style="color: #007700">.</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-23" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;32m'/wiki/index?database=projects'[0m</div>
                            <div class="cake-debug">[0;32m'GET'[0m</div>
                    </div>
    </div>
    <div id="stack-frame-24" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/tests/TestCase/Controller/WikiControllerTest.php&amp;line=93">ROOT/tests/TestCase/Controller/WikiControllerTest.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-24">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="89"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="90"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">loginUser</span><span style="color: #007700">(</span><span style="color: #DD0000">'reader'</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="91"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="92"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">restoreErrorHandlerMiddleware</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="93"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">get</span><span style="color: #007700">(</span><span style="color: #DD0000">"/wiki/index?database=projects"</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="94"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="95"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$compare&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">saveBodyToComparisonHtml</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="96"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">assertHtmlEqualsComparison</span><span style="color: #007700">(</span><span style="color: #0000BB">$compare</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="97"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-24" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;32m'/wiki/index?database=projects'[0m</div>
                    </div>
    </div>
    <div id="stack-frame-25" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/phpunit/phpunit/src/Framework/TestCase.php&amp;line=1471">ROOT/vendor/phpunit/phpunit/src/Framework/TestCase.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-25">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="1467"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="1468"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">registerMockObjectsFromTestArguments</span><span style="color: #007700">(</span><span style="color: #0000BB">$testArguments</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="1469"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="1470"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">try&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="1471"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$testResult&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;{</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">name</span><span style="color: #007700">}(...</span><span style="color: #0000BB">array_values</span><span style="color: #007700">(</span><span style="color: #0000BB">$testArguments</span><span style="color: #007700">));</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="1472"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}&nbsp;catch&nbsp;(</span><span style="color: #0000BB">Throwable&nbsp;$exception</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="1473"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(!</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">checkExceptionExpectations</span><span style="color: #007700">(</span><span style="color: #0000BB">$exception</span><span style="color: #007700">))&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="1474"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">throw&nbsp;</span><span style="color: #0000BB">$exception</span><span style="color: #007700">;</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="1475"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-25" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">No arguments</div>
                    </div>
    </div>
    <div id="stack-frame-26" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/phpunit/phpunit/src/Framework/TestCase.php&amp;line=1091">ROOT/vendor/phpunit/phpunit/src/Framework/TestCase.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-26">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="1087"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;{</span><span style="color: #0000BB">$method</span><span style="color: #007700">}();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="1088"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="1089"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="1090"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">assertPreConditions</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="1091"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">testResult&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">runTest</span><span style="color: #007700">();</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="1092"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">verifyMockObjects</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="1093"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">assertPostConditions</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="1094"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="1095"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(!empty(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">warnings</span><span style="color: #007700">))&nbsp;{</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-26" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">No arguments</div>
                    </div>
    </div>
    <div id="stack-frame-27" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/phpunit/phpunit/src/Framework/TestResult.php&amp;line=703">ROOT/vendor/phpunit/phpunit/src/Framework/TestResult.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-27">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="699"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="700"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$invoker&nbsp;</span><span style="color: #007700">=&nbsp;new&nbsp;</span><span style="color: #0000BB">Invoker</span><span style="color: #007700">;</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="701"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$invoker</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">invoke</span><span style="color: #007700">([</span><span style="color: #0000BB">$test</span><span style="color: #007700">,&nbsp;</span><span style="color: #DD0000">'runBare'</span><span style="color: #007700">],&nbsp;[],&nbsp;</span><span style="color: #0000BB">$_timeout</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="702"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}&nbsp;else&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="703"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$test</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">runBare</span><span style="color: #007700">();</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="704"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="705"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}&nbsp;catch&nbsp;(</span><span style="color: #0000BB">TimeoutException&nbsp;$e</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="706"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">addFailure</span><span style="color: #007700">(</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="707"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$test</span><span style="color: #007700">,</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-27" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">No arguments</div>
                    </div>
    </div>
    <div id="stack-frame-28" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/phpunit/phpunit/src/Framework/TestCase.php&amp;line=819">ROOT/vendor/phpunit/phpunit/src/Framework/TestCase.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-28">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="815"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="816"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$php&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">AbstractPhpProcess</span><span style="color: #007700">::</span><span style="color: #0000BB">factory</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="817"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$php</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">runTestJob</span><span style="color: #007700">(</span><span style="color: #0000BB">$template</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">render</span><span style="color: #007700">(),&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">$result</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="818"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}&nbsp;else&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="819"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$result</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">run</span><span style="color: #007700">(</span><span style="color: #0000BB">$this</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="820"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="821"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="822"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">result&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">null</span><span style="color: #007700">;</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="823"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-28" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mApp\Test\TestCase\Controller\WikiControllerTest[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;39mfixtures[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'app.Wiki'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'app.Users'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'app.Permissions'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'app.Databanks'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;32m'app.Pipelines'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m5[0m[0;90m => [0m[0;32m'plugin.Epi42.Token'[0m
  [0;90m][0m
  [0;39mcomparisonFile[0m[0;90m => [0m[0;32m'/var/www/html/tests/Comparisons/WikiControllerTest/testNotAuthorizedReader.php'[0m
  [0;39moverwriteComparison[0m[0;90m => [0m[1;33mfalse[0m
  [0;39mtestFile[0m[0;90m => [0m[1;33mnull[0m
  [0;39mfixtureManager[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\TestSuite\Fixture\FixtureManager[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m
    [0;35mprotected[0m [0;39m_initialized[0m[0;90m => [0m[1;33mtrue[0m
    [0;35mprotected[0m [0;39m_loaded[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_fixtureMap[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_insertionMap[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_processed[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_debug[0m[0;90m => [0m[1;33mfalse[0m
  [0;90m}[0m
  [0;39mautoFixtures[0m[0;90m => [0m[1;33mtrue[0m
  [0;39mdropTables[0m[0;90m => [0m[1;33mtrue[0m
  [0;39mtestdataFile[0m[0;90m => [0m[0;32m'/var/www/html/tests/Testdata/WikiControllerTest/testNotAuthorizedReader.php'[0m
  [0;35mprotected[0m [0;39m_configure[0m[0;90m => [0m[0;90m[[0m
    [0;32m'debug'[0m[0;90m => [0m[1;33mtrue[0m[0;90m,[0m
    [0;32m'Data'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'root'[0m[0;90m => [0m[0;32m'/var/www/html/tests/Files/'[0m[0;90m,[0m
      [0;32m'export'[0m[0;90m => [0m[0;32m'/var/www/html/tests/Files/jobs/'[0m[0;90m,[0m
      [0;32m'shared'[0m[0;90m => [0m[0;32m'/var/www/html/tests/Files/shared/'[0m[0;90m,[0m
      [0;32m'databases'[0m[0;90m => [0m[0;32m'/var/www/html/tests/Files/databases/'[0m[0;90m,[0m
      [0;32m'comparisons'[0m[0;90m => [0m[0;32m'/var/www/html/tests/Comparisons/'[0m[0;90m,[0m
      [0;32m'testdata'[0m[0;90m => [0m[0;32m'/var/www/html/tests/Testdata/'[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'namespace'[0m[0;90m => [0m[0;32m'App'[0m[0;90m,[0m
      [0;32m'encoding'[0m[0;90m => [0m[0;32m'UTF-8'[0m[0;90m,[0m
      [0;32m'defaultLocale'[0m[0;90m => [0m[0;32m'en_EN.UTF-8'[0m[0;90m,[0m
      [0;32m'defaultTimezone'[0m[0;90m => [0m[0;32m'UTC'[0m[0;90m,[0m
      [0;32m'base'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
      [0;32m'dir'[0m[0;90m => [0m[0;32m'src'[0m[0;90m,[0m
      [0;32m'webroot'[0m[0;90m => [0m[0;32m'htdocs'[0m[0;90m,[0m
      [0;32m'wwwRoot'[0m[0;90m => [0m[0;32m'/var/www/html/htdocs/'[0m[0;90m,[0m
      [0;32m'imageBaseUrl'[0m[0;90m => [0m[0;32m'img/'[0m[0;90m,[0m
      [0;32m'cssBaseUrl'[0m[0;90m => [0m[0;32m'css/'[0m[0;90m,[0m
      [0;32m'jsBaseUrl'[0m[0;90m => [0m[0;32m'js/'[0m[0;90m,[0m
      [0;32m'paths'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m
    [0;90m][0m[0;90m,[0m
    [0;32m'Security'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m[0;90m,[0m
    [0;32m'Asset'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'timestamp'[0m[0;90m => [0m[1;33mfalse[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'Error'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'errorLevel'[0m[0;90m => [0m[0;35m(int)[0m [1;34m32767[0m[0;90m,[0m
      [0;32m'exceptionRenderer'[0m[0;90m => [0m[0;32m'Cake\Error\ExceptionRenderer'[0m[0;90m,[0m
      [0;32m'skipLog'[0m[0;90m => [0m[0;90m[[0m
        [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
      [0;90m][0m[0;90m,[0m
      [0;32m'log'[0m[0;90m => [0m[1;33mtrue[0m[0;90m,[0m
      [0;32m'trace'[0m[0;90m => [0m[1;33mtrue[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'Session'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'defaults'[0m[0;90m => [0m[0;32m'cache'[0m[0;90m,[0m
      [0;32m'timeout'[0m[0;90m => [0m[0;35m(int)[0m [1;34m120[0m[0;90m,[0m
      [0;32m'cookie'[0m[0;90m => [0m[0;32m'EPIGRAF4'[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mbackupGlobals[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39mbackupGlobalsBlacklist[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mbackupStaticAttributes[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39mbackupStaticAttributesBlacklist[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39mrunTestInSeparateProcess[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprotected[0m [0;39mpreserveGlobalState[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprotected[0m [0;39m_tableLocator[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\ORM\Locator\TableLocator[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mlocations[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_config[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39minstances[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_fallbacked[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39moptions[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39m_appClass[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39m_appArgs[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39m_request[0m[0;90m => [0m[0;90m[[0m
    [0;32m'environment'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'HTTPS'[0m[0;90m => [0m[0;32m'on'[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_response[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39m_exception[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39m_session[0m[0;90m => [0m[0;90m[[0m
    [0;32m'Auth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'User'[0m[0;90m => [0m[0;90mobject([0m[0;36mApp\Model\Entity\User[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m[0;90m}[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_cookie[0m[0;90m => [0m[0;90m[[0m
    [0;32m'csrfToken'[0m[0;90m => [0m[0;32m'7b73937c883a3e5328f435b1b8dc115c9d46c3a9c41ecee565215c64'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_controller[0m[0;90m => [0m[0;90mobject([0m[0;36mApp\Controller\ErrorController[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m
    [0;39mmenu[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;39msidemenu[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;39mallowedDatabases[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;39mactiveDatabase[0m[0;90m => [0m[1;33mfalse[0m
    [0;39maccessMode[0m[0;90m => [0m[0;32m'guest'[0m
    [0;39mauthorized[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;39mpaginate[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;39mRequestHandler[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\Component\RequestHandlerComponent[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39mname[0m[0;90m => [0m[0;32m'Wiki'[0m
    [0;35mprotected[0m [0;39mrequest[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\ServerRequest[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39mresponse[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Http\Response[0m[0;90m) id:[0m[1;34m7[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_responseClass[0m[0;90m => [0m[0;32m'Cake\Http\Response'[0m
    [0;35mprotected[0m [0;39mautoRender[0m[0;90m => [0m[1;33mfalse[0m
    [0;35mprotected[0m [0;39m_components[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Controller\ComponentRegistry[0m[0;90m) id:[0m[1;34m8[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39mplugin[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39m_eventManager[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\Event\EventManager[0m[0;90m) id:[0m[1;34m9[0m[0;90m {[0m[0;90m}[0m
    [0;35mprotected[0m [0;39m_eventClass[0m[0;90m => [0m[0;32m'Cake\Event\Event'[0m
    [0;35mprotected[0m [0;39m_tableLocator[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\ORM\Locator\TableLocator[0m[0;90m) id:[0m[1;34m2[0m[0;90m {}[0m
    [0;35mprotected[0m [0;39mmodelClass[0m[0;90m => [0m[0;32m'Wiki'[0m
    [0;35mprotected[0m [0;39m_modelFactories[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39m_modelType[0m[0;90m => [0m[0;32m'Table'[0m
    [0;35mprotected[0m [0;39m_viewBuilder[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\View\ViewBuilder[0m[0;90m) id:[0m[1;34m10[0m[0;90m {[0m[0;90m}[0m
  [0;90m}[0m
  [0;35mprotected[0m [0;39m_viewName[0m[0;90m => [0m[0;32m'/var/www/html/templates/Error/error400.php'[0m
  [0;35mprotected[0m [0;39m_layoutName[0m[0;90m => [0m[0;32m'/var/www/html/vendor/cakephp/cakephp/templates/layout/dev_error.php'[0m
  [0;35mprotected[0m [0;39m_requestSession[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39m_securityToken[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprotected[0m [0;39m_csrfToken[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprotected[0m [0;39m_retainFlashMessages[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprotected[0m [0;39m_flashMessages[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39m_cookieEncryptionKey[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39m_unlockedFields[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprotected[0m [0;39m_validCiphers[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'aes'[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39m_compareBasePath[0m[0;90m => [0m[0;32m'/var/www/html/tests/Comparisons/WikiControllerTest/'[0m
  [0;35mprotected[0m [0;39m_updateComparisons[0m[0;90m => [0m[1;33mnull[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-29" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/phpunit/phpunit/src/Framework/TestSuite.php&amp;line=627">ROOT/vendor/phpunit/phpunit/src/Framework/TestSuite.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-29">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="623"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$test</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">setBackupStaticAttributes</span><span style="color: #007700">(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">backupStaticAttributes</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="624"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$test</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">setRunTestInSeparateProcess</span><span style="color: #007700">(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">runTestInSeparateProcess</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="625"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="626"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="627"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$test</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">run</span><span style="color: #007700">(</span><span style="color: #0000BB">$result</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="628"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="629"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="630"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">try&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="631"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">foreach&nbsp;(</span><span style="color: #0000BB">$hookMethods</span><span style="color: #007700">[</span><span style="color: #DD0000">'afterClass'</span><span style="color: #007700">]&nbsp;as&nbsp;</span><span style="color: #0000BB">$afterClassMethod</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-29" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mPHPUnit\Framework\TestResult[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;35mprivate[0m [0;39mpassed[0m[0;90m => [0m[0;90m[[0m
    [0;32m'App\Test\TestCase\Controller\ArticlesControllerTest::testSearchProjectI'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\ArticlesControllerTest::testSearchProjectII'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\ArticlesControllerTest::testSearch'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\ArticlesControllerTest::testShow'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\ArticlesControllerTest::testShowNoAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testHomepage'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testViewAuthor'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testViewRedirectNoAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testViewRedirectTokenAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testViewWrongToken'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDisplayStaticAdmin'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDisplayStaticAuthor'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDisplayStaticRedirectNoAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDisplayMissingPageAdmin'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDisplayMissingPageNoAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testShowStart'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testHelp'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testShow'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testAdd'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testEdit'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testUnlock'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDelete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testIndex'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testDownload'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportArticle'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportBook'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportNoAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testAdd'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportTokenRedirect'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportWrongToken'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportExecute'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testFileExists'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testIndexWithId'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testIndexWithPath'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testIndexSortDefault'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testIndexSortAsc'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testIndexSortDesc'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testSelect'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDownloadWithIdAuthor'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDownloadWithIdGuest'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDownloadWithPathAuthor'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDownloadWithPathGuest'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testView'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testMove'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testEditfolder'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testEdit'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDisplayWithPath'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDelete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testNewfolder'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testView'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testAddElement'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testAddOption'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testAdd'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testEdit'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testDelete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprivate[0m [0;39merrors[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprivate[0m [0;39mfailures[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m5[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m[0;90m}[0m
  [0;90m][0m
  [0;35mprivate[0m [0;39mwarnings[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprivate[0m [0;39mnotImplemented[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m7[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m8[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m9[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m10[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m11[0m[0;90m {[0m[0;90m}[0m
  [0;90m][0m
  [0;35mprivate[0m [0;39mrisky[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprivate[0m [0;39mskipped[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprivate[0m [0;39mlisteners[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Runner\TestListenerAdapter[0m[0;90m) id:[0m[1;34m12[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\TestSuite\Fixture\FixtureInjector[0m[0;90m) id:[0m[1;34m13[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Util\Log\TeamCity[0m[0;90m) id:[0m[1;34m14[0m[0;90m {[0m[0;90m}[0m
  [0;90m][0m
  [0;35mprivate[0m [0;39mrunTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m67[0m
  [0;35mprivate[0m [0;39mtime[0m[0;90m => [0m[0;35m(float)[0m [1;34m8.3049647808075[0m
  [0;35mprivate[0m [0;39mtopTestSuite[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestSuite[0m[0;90m) id:[0m[1;34m15[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mbackupGlobals[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39mbackupStaticAttributes[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39mrunTestInSeparateProcess[0m[0;90m => [0m[1;33mfalse[0m
    [0;35mprotected[0m [0;39mname[0m[0;90m => [0m[0;32m''[0m
    [0;35mprotected[0m [0;39mgroups[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mtests[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mnumTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;35mprotected[0m [0;39mtestCase[0m[0;90m => [0m[1;33mfalse[0m
    [0;35mprotected[0m [0;39mfoundClasses[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprivate[0m [0;39mcachedNumTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m227[0m
    [0;35mprivate[0m [0;39mbeStrictAboutChangesToGlobalState[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprivate[0m [0;39miteratorFilter[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprivate[0m [0;39mdeclaredClasses[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
  [0;90m}[0m
  [0;35mprivate[0m [0;39mcodeCoverage[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprivate[0m [0;39mconvertDeprecationsToExceptions[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprivate[0m [0;39mconvertErrorsToExceptions[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprivate[0m [0;39mconvertNoticesToExceptions[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprivate[0m [0;39mconvertWarningsToExceptions[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprivate[0m [0;39mstop[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mstopOnError[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mstopOnFailure[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mstopOnWarning[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mbeStrictAboutTestsThatDoNotTestAnything[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprivate[0m [0;39mbeStrictAboutOutputDuringTests[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mbeStrictAboutTodoAnnotatedTests[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mbeStrictAboutResourceUsageDuringSmallTests[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39menforceTimeLimit[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mtimeoutForSmallTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m1[0m
  [0;35mprivate[0m [0;39mtimeoutForMediumTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m10[0m
  [0;35mprivate[0m [0;39mtimeoutForLargeTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m60[0m
  [0;35mprivate[0m [0;39mstopOnRisky[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mstopOnIncomplete[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mstopOnSkipped[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mlastTestFailed[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mdefaultTimeLimit[0m[0;90m => [0m[0;35m(int)[0m [1;34m0[0m
  [0;35mprivate[0m [0;39mstopOnDefect[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mregisterMockObjectsFromTestArgumentsRecursively[0m[0;90m => [0m[1;33mfalse[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-30" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/phpunit/phpunit/src/Framework/TestSuite.php&amp;line=627">ROOT/vendor/phpunit/phpunit/src/Framework/TestSuite.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-30">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="623"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$test</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">setBackupStaticAttributes</span><span style="color: #007700">(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">backupStaticAttributes</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="624"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$test</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">setRunTestInSeparateProcess</span><span style="color: #007700">(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">runTestInSeparateProcess</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="625"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="626"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="627"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$test</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">run</span><span style="color: #007700">(</span><span style="color: #0000BB">$result</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="628"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="629"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="630"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">try&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="631"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">foreach&nbsp;(</span><span style="color: #0000BB">$hookMethods</span><span style="color: #007700">[</span><span style="color: #DD0000">'afterClass'</span><span style="color: #007700">]&nbsp;as&nbsp;</span><span style="color: #0000BB">$afterClassMethod</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-30" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mPHPUnit\Framework\TestResult[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;35mprivate[0m [0;39mpassed[0m[0;90m => [0m[0;90m[[0m
    [0;32m'App\Test\TestCase\Controller\ArticlesControllerTest::testSearchProjectI'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\ArticlesControllerTest::testSearchProjectII'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\ArticlesControllerTest::testSearch'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\ArticlesControllerTest::testShow'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\ArticlesControllerTest::testShowNoAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testHomepage'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testViewAuthor'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testViewRedirectNoAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testViewRedirectTokenAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testViewWrongToken'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDisplayStaticAdmin'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDisplayStaticAuthor'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDisplayStaticRedirectNoAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDisplayMissingPageAdmin'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDisplayMissingPageNoAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testShowStart'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testHelp'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testShow'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testAdd'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testEdit'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testUnlock'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDelete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testIndex'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testDownload'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportArticle'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportBook'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportNoAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testAdd'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportTokenRedirect'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportWrongToken'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportExecute'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testFileExists'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testIndexWithId'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testIndexWithPath'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testIndexSortDefault'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testIndexSortAsc'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testIndexSortDesc'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testSelect'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDownloadWithIdAuthor'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDownloadWithIdGuest'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDownloadWithPathAuthor'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDownloadWithPathGuest'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testView'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testMove'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testEditfolder'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testEdit'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDisplayWithPath'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDelete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testNewfolder'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testView'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testAddElement'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testAddOption'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testAdd'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testEdit'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testDelete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprivate[0m [0;39merrors[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprivate[0m [0;39mfailures[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m5[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m[0;90m}[0m
  [0;90m][0m
  [0;35mprivate[0m [0;39mwarnings[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprivate[0m [0;39mnotImplemented[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m7[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m8[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m9[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m10[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m11[0m[0;90m {[0m[0;90m}[0m
  [0;90m][0m
  [0;35mprivate[0m [0;39mrisky[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprivate[0m [0;39mskipped[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprivate[0m [0;39mlisteners[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Runner\TestListenerAdapter[0m[0;90m) id:[0m[1;34m12[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\TestSuite\Fixture\FixtureInjector[0m[0;90m) id:[0m[1;34m13[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Util\Log\TeamCity[0m[0;90m) id:[0m[1;34m14[0m[0;90m {[0m[0;90m}[0m
  [0;90m][0m
  [0;35mprivate[0m [0;39mrunTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m67[0m
  [0;35mprivate[0m [0;39mtime[0m[0;90m => [0m[0;35m(float)[0m [1;34m8.3049647808075[0m
  [0;35mprivate[0m [0;39mtopTestSuite[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestSuite[0m[0;90m) id:[0m[1;34m15[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mbackupGlobals[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39mbackupStaticAttributes[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39mrunTestInSeparateProcess[0m[0;90m => [0m[1;33mfalse[0m
    [0;35mprotected[0m [0;39mname[0m[0;90m => [0m[0;32m''[0m
    [0;35mprotected[0m [0;39mgroups[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mtests[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mnumTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;35mprotected[0m [0;39mtestCase[0m[0;90m => [0m[1;33mfalse[0m
    [0;35mprotected[0m [0;39mfoundClasses[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprivate[0m [0;39mcachedNumTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m227[0m
    [0;35mprivate[0m [0;39mbeStrictAboutChangesToGlobalState[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprivate[0m [0;39miteratorFilter[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprivate[0m [0;39mdeclaredClasses[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
  [0;90m}[0m
  [0;35mprivate[0m [0;39mcodeCoverage[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprivate[0m [0;39mconvertDeprecationsToExceptions[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprivate[0m [0;39mconvertErrorsToExceptions[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprivate[0m [0;39mconvertNoticesToExceptions[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprivate[0m [0;39mconvertWarningsToExceptions[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprivate[0m [0;39mstop[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mstopOnError[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mstopOnFailure[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mstopOnWarning[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mbeStrictAboutTestsThatDoNotTestAnything[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprivate[0m [0;39mbeStrictAboutOutputDuringTests[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mbeStrictAboutTodoAnnotatedTests[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mbeStrictAboutResourceUsageDuringSmallTests[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39menforceTimeLimit[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mtimeoutForSmallTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m1[0m
  [0;35mprivate[0m [0;39mtimeoutForMediumTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m10[0m
  [0;35mprivate[0m [0;39mtimeoutForLargeTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m60[0m
  [0;35mprivate[0m [0;39mstopOnRisky[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mstopOnIncomplete[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mstopOnSkipped[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mlastTestFailed[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mdefaultTimeLimit[0m[0;90m => [0m[0;35m(int)[0m [1;34m0[0m
  [0;35mprivate[0m [0;39mstopOnDefect[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mregisterMockObjectsFromTestArgumentsRecursively[0m[0;90m => [0m[1;33mfalse[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-31" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/phpunit/phpunit/src/Framework/TestSuite.php&amp;line=627">ROOT/vendor/phpunit/phpunit/src/Framework/TestSuite.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-31">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="623"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$test</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">setBackupStaticAttributes</span><span style="color: #007700">(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">backupStaticAttributes</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="624"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$test</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">setRunTestInSeparateProcess</span><span style="color: #007700">(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">runTestInSeparateProcess</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="625"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="626"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="627"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$test</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">run</span><span style="color: #007700">(</span><span style="color: #0000BB">$result</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="628"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="629"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="630"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">try&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="631"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">foreach&nbsp;(</span><span style="color: #0000BB">$hookMethods</span><span style="color: #007700">[</span><span style="color: #DD0000">'afterClass'</span><span style="color: #007700">]&nbsp;as&nbsp;</span><span style="color: #0000BB">$afterClassMethod</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-31" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mPHPUnit\Framework\TestResult[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;35mprivate[0m [0;39mpassed[0m[0;90m => [0m[0;90m[[0m
    [0;32m'App\Test\TestCase\Controller\ArticlesControllerTest::testSearchProjectI'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\ArticlesControllerTest::testSearchProjectII'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\ArticlesControllerTest::testSearch'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\ArticlesControllerTest::testShow'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\ArticlesControllerTest::testShowNoAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testHomepage'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testViewAuthor'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testViewRedirectNoAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testViewRedirectTokenAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testViewWrongToken'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDisplayStaticAdmin'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDisplayStaticAuthor'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDisplayStaticRedirectNoAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDisplayMissingPageAdmin'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDisplayMissingPageNoAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testShowStart'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testHelp'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testShow'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testAdd'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testEdit'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testUnlock'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDelete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testIndex'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testDownload'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportArticle'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportBook'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportNoAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testAdd'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportTokenRedirect'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportWrongToken'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportExecute'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testFileExists'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testIndexWithId'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testIndexWithPath'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testIndexSortDefault'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testIndexSortAsc'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testIndexSortDesc'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testSelect'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDownloadWithIdAuthor'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDownloadWithIdGuest'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDownloadWithPathAuthor'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDownloadWithPathGuest'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testView'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testMove'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testEditfolder'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testEdit'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDisplayWithPath'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDelete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testNewfolder'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testView'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testAddElement'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testAddOption'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testAdd'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testEdit'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testDelete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprivate[0m [0;39merrors[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprivate[0m [0;39mfailures[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m5[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m[0;90m}[0m
  [0;90m][0m
  [0;35mprivate[0m [0;39mwarnings[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprivate[0m [0;39mnotImplemented[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m7[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m8[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m9[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m10[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m11[0m[0;90m {[0m[0;90m}[0m
  [0;90m][0m
  [0;35mprivate[0m [0;39mrisky[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprivate[0m [0;39mskipped[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprivate[0m [0;39mlisteners[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Runner\TestListenerAdapter[0m[0;90m) id:[0m[1;34m12[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\TestSuite\Fixture\FixtureInjector[0m[0;90m) id:[0m[1;34m13[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Util\Log\TeamCity[0m[0;90m) id:[0m[1;34m14[0m[0;90m {[0m[0;90m}[0m
  [0;90m][0m
  [0;35mprivate[0m [0;39mrunTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m67[0m
  [0;35mprivate[0m [0;39mtime[0m[0;90m => [0m[0;35m(float)[0m [1;34m8.3049647808075[0m
  [0;35mprivate[0m [0;39mtopTestSuite[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestSuite[0m[0;90m) id:[0m[1;34m15[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mbackupGlobals[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39mbackupStaticAttributes[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39mrunTestInSeparateProcess[0m[0;90m => [0m[1;33mfalse[0m
    [0;35mprotected[0m [0;39mname[0m[0;90m => [0m[0;32m''[0m
    [0;35mprotected[0m [0;39mgroups[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mtests[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mnumTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;35mprotected[0m [0;39mtestCase[0m[0;90m => [0m[1;33mfalse[0m
    [0;35mprotected[0m [0;39mfoundClasses[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprivate[0m [0;39mcachedNumTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m227[0m
    [0;35mprivate[0m [0;39mbeStrictAboutChangesToGlobalState[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprivate[0m [0;39miteratorFilter[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprivate[0m [0;39mdeclaredClasses[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
  [0;90m}[0m
  [0;35mprivate[0m [0;39mcodeCoverage[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprivate[0m [0;39mconvertDeprecationsToExceptions[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprivate[0m [0;39mconvertErrorsToExceptions[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprivate[0m [0;39mconvertNoticesToExceptions[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprivate[0m [0;39mconvertWarningsToExceptions[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprivate[0m [0;39mstop[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mstopOnError[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mstopOnFailure[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mstopOnWarning[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mbeStrictAboutTestsThatDoNotTestAnything[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprivate[0m [0;39mbeStrictAboutOutputDuringTests[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mbeStrictAboutTodoAnnotatedTests[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mbeStrictAboutResourceUsageDuringSmallTests[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39menforceTimeLimit[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mtimeoutForSmallTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m1[0m
  [0;35mprivate[0m [0;39mtimeoutForMediumTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m10[0m
  [0;35mprivate[0m [0;39mtimeoutForLargeTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m60[0m
  [0;35mprivate[0m [0;39mstopOnRisky[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mstopOnIncomplete[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mstopOnSkipped[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mlastTestFailed[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mdefaultTimeLimit[0m[0;90m => [0m[0;35m(int)[0m [1;34m0[0m
  [0;35mprivate[0m [0;39mstopOnDefect[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mregisterMockObjectsFromTestArgumentsRecursively[0m[0;90m => [0m[1;33mfalse[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-32" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/phpunit/phpunit/src/TextUI/TestRunner.php&amp;line=656">ROOT/vendor/phpunit/phpunit/src/TextUI/TestRunner.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-32">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="652"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$extension</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">executeBeforeFirstTest</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="653"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="654"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="655"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="656"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$suite</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">run</span><span style="color: #007700">(</span><span style="color: #0000BB">$result</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="657"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="658"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">foreach&nbsp;(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">extensions&nbsp;</span><span style="color: #007700">as&nbsp;</span><span style="color: #0000BB">$extension</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="659"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">if&nbsp;(</span><span style="color: #0000BB">$extension&nbsp;</span><span style="color: #007700">instanceof&nbsp;</span><span style="color: #0000BB">AfterLastTestHook</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="660"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$extension</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">executeAfterLastTest</span><span style="color: #007700">();</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-32" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mPHPUnit\Framework\TestResult[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;35mprivate[0m [0;39mpassed[0m[0;90m => [0m[0;90m[[0m
    [0;32m'App\Test\TestCase\Controller\ArticlesControllerTest::testSearchProjectI'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\ArticlesControllerTest::testSearchProjectII'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\ArticlesControllerTest::testSearch'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\ArticlesControllerTest::testShow'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\ArticlesControllerTest::testShowNoAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testHomepage'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testViewAuthor'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testViewRedirectNoAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testViewRedirectTokenAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testViewWrongToken'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDisplayStaticAdmin'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDisplayStaticAuthor'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDisplayStaticRedirectNoAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDisplayMissingPageAdmin'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDisplayMissingPageNoAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testShowStart'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testHelp'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testShow'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testAdd'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testEdit'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testUnlock'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\DocsControllerTest::testDelete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testIndex'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testDownload'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportArticle'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportBook'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportNoAuth'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testAdd'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportTokenRedirect'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportWrongToken'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\JobsControllerTest::testExportExecute'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testFileExists'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testIndexWithId'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testIndexWithPath'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testIndexSortDefault'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testIndexSortAsc'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testIndexSortDesc'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testSelect'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDownloadWithIdAuthor'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDownloadWithIdGuest'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDownloadWithPathAuthor'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDownloadWithPathGuest'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testView'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testMove'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testEditfolder'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testEdit'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDisplayWithPath'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testDelete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\FilesControllerTest::testNewfolder'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testView'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testAddElement'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testAddOption'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testAdd'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testEdit'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m[0;90m,[0m
    [0;32m'App\Test\TestCase\Controller\PipelinesControllerTest::testDelete'[0m[0;90m => [0m[0;90m[[0m
      [0;32m'result'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
      [0;32m'size'[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprivate[0m [0;39merrors[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprivate[0m [0;39mfailures[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m5[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m6[0m[0;90m {[0m[0;90m}[0m
  [0;90m][0m
  [0;35mprivate[0m [0;39mwarnings[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprivate[0m [0;39mnotImplemented[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m7[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m8[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m9[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m10[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestFailure[0m[0;90m) id:[0m[1;34m11[0m[0;90m {[0m[0;90m}[0m
  [0;90m][0m
  [0;35mprivate[0m [0;39mrisky[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprivate[0m [0;39mskipped[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprivate[0m [0;39mlisteners[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Runner\TestListenerAdapter[0m[0;90m) id:[0m[1;34m12[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\TestSuite\Fixture\FixtureInjector[0m[0;90m) id:[0m[1;34m13[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Util\Log\TeamCity[0m[0;90m) id:[0m[1;34m14[0m[0;90m {[0m[0;90m}[0m
  [0;90m][0m
  [0;35mprivate[0m [0;39mrunTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m67[0m
  [0;35mprivate[0m [0;39mtime[0m[0;90m => [0m[0;35m(float)[0m [1;34m8.3049647808075[0m
  [0;35mprivate[0m [0;39mtopTestSuite[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestSuite[0m[0;90m) id:[0m[1;34m15[0m[0;90m {[0m
    [0;35mprotected[0m [0;39mbackupGlobals[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39mbackupStaticAttributes[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprotected[0m [0;39mrunTestInSeparateProcess[0m[0;90m => [0m[1;33mfalse[0m
    [0;35mprotected[0m [0;39mname[0m[0;90m => [0m[0;32m''[0m
    [0;35mprotected[0m [0;39mgroups[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mtests[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprotected[0m [0;39mnumTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
    [0;35mprotected[0m [0;39mtestCase[0m[0;90m => [0m[1;33mfalse[0m
    [0;35mprotected[0m [0;39mfoundClasses[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
    [0;35mprivate[0m [0;39mcachedNumTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m227[0m
    [0;35mprivate[0m [0;39mbeStrictAboutChangesToGlobalState[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprivate[0m [0;39miteratorFilter[0m[0;90m => [0m[1;33mnull[0m
    [0;35mprivate[0m [0;39mdeclaredClasses[0m[0;90m => [0m[0;90m[[0m
      [0;32m''[0m[0;90m => [0m[0;31m[maximum depth reached][0m
    [0;90m][0m
  [0;90m}[0m
  [0;35mprivate[0m [0;39mcodeCoverage[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprivate[0m [0;39mconvertDeprecationsToExceptions[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprivate[0m [0;39mconvertErrorsToExceptions[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprivate[0m [0;39mconvertNoticesToExceptions[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprivate[0m [0;39mconvertWarningsToExceptions[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprivate[0m [0;39mstop[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mstopOnError[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mstopOnFailure[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mstopOnWarning[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mbeStrictAboutTestsThatDoNotTestAnything[0m[0;90m => [0m[1;33mtrue[0m
  [0;35mprivate[0m [0;39mbeStrictAboutOutputDuringTests[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mbeStrictAboutTodoAnnotatedTests[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mbeStrictAboutResourceUsageDuringSmallTests[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39menforceTimeLimit[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mtimeoutForSmallTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m1[0m
  [0;35mprivate[0m [0;39mtimeoutForMediumTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m10[0m
  [0;35mprivate[0m [0;39mtimeoutForLargeTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m60[0m
  [0;35mprivate[0m [0;39mstopOnRisky[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mstopOnIncomplete[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mstopOnSkipped[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mlastTestFailed[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mdefaultTimeLimit[0m[0;90m => [0m[0;35m(int)[0m [1;34m0[0m
  [0;35mprivate[0m [0;39mstopOnDefect[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprivate[0m [0;39mregisterMockObjectsFromTestArgumentsRecursively[0m[0;90m => [0m[1;33mfalse[0m
[0;90m}[0m</div>
                    </div>
    </div>
    <div id="stack-frame-33" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/phpunit/phpunit/src/TextUI/Command.php&amp;line=236">ROOT/vendor/phpunit/phpunit/src/TextUI/Command.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-33">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="232"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="233"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">unset(</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">arguments</span><span style="color: #007700">[</span><span style="color: #DD0000">'test'</span><span style="color: #007700">],&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">arguments</span><span style="color: #007700">[</span><span style="color: #DD0000">'testFile'</span><span style="color: #007700">]);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="234"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="235"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">try&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="236"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$result&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">$runner</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">doRun</span><span style="color: #007700">(</span><span style="color: #0000BB">$suite</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">arguments</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">$this</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">warnings</span><span style="color: #007700">,&nbsp;</span><span style="color: #0000BB">$exit</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="237"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}&nbsp;catch&nbsp;(</span><span style="color: #0000BB">Exception&nbsp;$e</span><span style="color: #007700">)&nbsp;{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="238"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">print&nbsp;</span><span style="color: #0000BB">$e</span><span style="color: #007700">-&gt;</span><span style="color: #0000BB">getMessage</span><span style="color: #007700">()&nbsp;.&nbsp;</span><span style="color: #0000BB">PHP_EOL</span><span style="color: #007700">;</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="239"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="240"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-33" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90mobject([0m[0;36mPHPUnit\Framework\TestSuite[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
  [0;35mprotected[0m [0;39mbackupGlobals[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39mbackupStaticAttributes[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprotected[0m [0;39mrunTestInSeparateProcess[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprotected[0m [0;39mname[0m[0;90m => [0m[0;32m''[0m
  [0;35mprotected[0m [0;39mgroups[0m[0;90m => [0m[0;90m[[0m
    [0;32m'default'[0m[0;90m => [0m[0;90m[[0m
      [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestSuite[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestSuite[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestSuite[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestSuite[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m[0;90m}[0m[0;90m,[0m
      [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestSuite[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m[0;90m}[0m
    [0;90m][0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mtests[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestSuite[0m[0;90m) id:[0m[1;34m1[0m[0;90m {}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestSuite[0m[0;90m) id:[0m[1;34m2[0m[0;90m {}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestSuite[0m[0;90m) id:[0m[1;34m3[0m[0;90m {}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestSuite[0m[0;90m) id:[0m[1;34m4[0m[0;90m {}[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestSuite[0m[0;90m) id:[0m[1;34m5[0m[0;90m {}[0m
  [0;90m][0m
  [0;35mprotected[0m [0;39mnumTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m-1[0m
  [0;35mprotected[0m [0;39mtestCase[0m[0;90m => [0m[1;33mfalse[0m
  [0;35mprotected[0m [0;39mfoundClasses[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;35mprivate[0m [0;39mcachedNumTests[0m[0;90m => [0m[0;35m(int)[0m [1;34m227[0m
  [0;35mprivate[0m [0;39mbeStrictAboutChangesToGlobalState[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprivate[0m [0;39miteratorFilter[0m[0;90m => [0m[1;33mnull[0m
  [0;35mprivate[0m [0;39mdeclaredClasses[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'stdClass'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'Exception'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'ErrorException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'Error'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m4[0m[0;90m => [0m[0;32m'CompileError'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m5[0m[0;90m => [0m[0;32m'ParseError'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m6[0m[0;90m => [0m[0;32m'TypeError'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m7[0m[0;90m => [0m[0;32m'ArgumentCountError'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m8[0m[0;90m => [0m[0;32m'ArithmeticError'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m9[0m[0;90m => [0m[0;32m'DivisionByZeroError'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m10[0m[0;90m => [0m[0;32m'Closure'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m11[0m[0;90m => [0m[0;32m'Generator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m12[0m[0;90m => [0m[0;32m'ClosedGeneratorException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m13[0m[0;90m => [0m[0;32m'WeakReference'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m14[0m[0;90m => [0m[0;32m'DateTime'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m15[0m[0;90m => [0m[0;32m'DateTimeImmutable'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m16[0m[0;90m => [0m[0;32m'DateTimeZone'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m17[0m[0;90m => [0m[0;32m'DateInterval'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m18[0m[0;90m => [0m[0;32m'DatePeriod'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m19[0m[0;90m => [0m[0;32m'LibXMLError'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m20[0m[0;90m => [0m[0;32m'SQLite3'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m21[0m[0;90m => [0m[0;32m'SQLite3Stmt'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m22[0m[0;90m => [0m[0;32m'SQLite3Result'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m23[0m[0;90m => [0m[0;32m'CURLFile'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m24[0m[0;90m => [0m[0;32m'DOMException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m25[0m[0;90m => [0m[0;32m'DOMStringList'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m26[0m[0;90m => [0m[0;32m'DOMNameList'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m27[0m[0;90m => [0m[0;32m'DOMImplementationList'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m28[0m[0;90m => [0m[0;32m'DOMImplementationSource'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m29[0m[0;90m => [0m[0;32m'DOMImplementation'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m30[0m[0;90m => [0m[0;32m'DOMNode'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m31[0m[0;90m => [0m[0;32m'DOMNameSpaceNode'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m32[0m[0;90m => [0m[0;32m'DOMDocumentFragment'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m33[0m[0;90m => [0m[0;32m'DOMDocument'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m34[0m[0;90m => [0m[0;32m'DOMNodeList'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m35[0m[0;90m => [0m[0;32m'DOMNamedNodeMap'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m36[0m[0;90m => [0m[0;32m'DOMCharacterData'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m37[0m[0;90m => [0m[0;32m'DOMAttr'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m38[0m[0;90m => [0m[0;32m'DOMElement'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m39[0m[0;90m => [0m[0;32m'DOMText'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m40[0m[0;90m => [0m[0;32m'DOMComment'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m41[0m[0;90m => [0m[0;32m'DOMTypeinfo'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m42[0m[0;90m => [0m[0;32m'DOMUserDataHandler'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m43[0m[0;90m => [0m[0;32m'DOMDomError'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m44[0m[0;90m => [0m[0;32m'DOMErrorHandler'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m45[0m[0;90m => [0m[0;32m'DOMLocator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m46[0m[0;90m => [0m[0;32m'DOMConfiguration'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m47[0m[0;90m => [0m[0;32m'DOMCdataSection'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m48[0m[0;90m => [0m[0;32m'DOMDocumentType'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m49[0m[0;90m => [0m[0;32m'DOMNotation'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m50[0m[0;90m => [0m[0;32m'DOMEntity'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m51[0m[0;90m => [0m[0;32m'DOMEntityReference'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m52[0m[0;90m => [0m[0;32m'DOMProcessingInstruction'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m53[0m[0;90m => [0m[0;32m'DOMStringExtend'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m54[0m[0;90m => [0m[0;32m'DOMXPath'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m55[0m[0;90m => [0m[0;32m'finfo'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m56[0m[0;90m => [0m[0;32m'HashContext'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m57[0m[0;90m => [0m[0;32m'JsonException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m58[0m[0;90m => [0m[0;32m'LogicException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m59[0m[0;90m => [0m[0;32m'BadFunctionCallException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m60[0m[0;90m => [0m[0;32m'BadMethodCallException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m61[0m[0;90m => [0m[0;32m'DomainException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m62[0m[0;90m => [0m[0;32m'InvalidArgumentException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m63[0m[0;90m => [0m[0;32m'LengthException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m64[0m[0;90m => [0m[0;32m'OutOfRangeException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m65[0m[0;90m => [0m[0;32m'RuntimeException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m66[0m[0;90m => [0m[0;32m'OutOfBoundsException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m67[0m[0;90m => [0m[0;32m'OverflowException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m68[0m[0;90m => [0m[0;32m'RangeException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m69[0m[0;90m => [0m[0;32m'UnderflowException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m70[0m[0;90m => [0m[0;32m'UnexpectedValueException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m71[0m[0;90m => [0m[0;32m'RecursiveIteratorIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m72[0m[0;90m => [0m[0;32m'IteratorIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m73[0m[0;90m => [0m[0;32m'FilterIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m74[0m[0;90m => [0m[0;32m'RecursiveFilterIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m75[0m[0;90m => [0m[0;32m'CallbackFilterIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m76[0m[0;90m => [0m[0;32m'RecursiveCallbackFilterIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m77[0m[0;90m => [0m[0;32m'ParentIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m78[0m[0;90m => [0m[0;32m'LimitIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m79[0m[0;90m => [0m[0;32m'CachingIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m80[0m[0;90m => [0m[0;32m'RecursiveCachingIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m81[0m[0;90m => [0m[0;32m'NoRewindIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m82[0m[0;90m => [0m[0;32m'AppendIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m83[0m[0;90m => [0m[0;32m'InfiniteIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m84[0m[0;90m => [0m[0;32m'RegexIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m85[0m[0;90m => [0m[0;32m'RecursiveRegexIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m86[0m[0;90m => [0m[0;32m'EmptyIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m87[0m[0;90m => [0m[0;32m'RecursiveTreeIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m88[0m[0;90m => [0m[0;32m'ArrayObject'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m89[0m[0;90m => [0m[0;32m'ArrayIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m90[0m[0;90m => [0m[0;32m'RecursiveArrayIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m91[0m[0;90m => [0m[0;32m'SplFileInfo'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m92[0m[0;90m => [0m[0;32m'DirectoryIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m93[0m[0;90m => [0m[0;32m'FilesystemIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m94[0m[0;90m => [0m[0;32m'RecursiveDirectoryIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m95[0m[0;90m => [0m[0;32m'GlobIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m96[0m[0;90m => [0m[0;32m'SplFileObject'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m97[0m[0;90m => [0m[0;32m'SplTempFileObject'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m98[0m[0;90m => [0m[0;32m'SplDoublyLinkedList'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m99[0m[0;90m => [0m[0;32m'SplQueue'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m100[0m[0;90m => [0m[0;32m'SplStack'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m101[0m[0;90m => [0m[0;32m'SplHeap'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m102[0m[0;90m => [0m[0;32m'SplMinHeap'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m103[0m[0;90m => [0m[0;32m'SplMaxHeap'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m104[0m[0;90m => [0m[0;32m'SplPriorityQueue'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m105[0m[0;90m => [0m[0;32m'SplFixedArray'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m106[0m[0;90m => [0m[0;32m'SplObjectStorage'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m107[0m[0;90m => [0m[0;32m'MultipleIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m108[0m[0;90m => [0m[0;32m'PDOException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m109[0m[0;90m => [0m[0;32m'PDO'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m110[0m[0;90m => [0m[0;32m'PDOStatement'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m111[0m[0;90m => [0m[0;32m'PDORow'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m112[0m[0;90m => [0m[0;32m'SessionHandler'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m113[0m[0;90m => [0m[0;32m'ReflectionException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m114[0m[0;90m => [0m[0;32m'Reflection'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m115[0m[0;90m => [0m[0;32m'ReflectionFunctionAbstract'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m116[0m[0;90m => [0m[0;32m'ReflectionFunction'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m117[0m[0;90m => [0m[0;32m'ReflectionGenerator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m118[0m[0;90m => [0m[0;32m'ReflectionParameter'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m119[0m[0;90m => [0m[0;32m'ReflectionType'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m120[0m[0;90m => [0m[0;32m'ReflectionNamedType'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m121[0m[0;90m => [0m[0;32m'ReflectionMethod'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m122[0m[0;90m => [0m[0;32m'ReflectionClass'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m123[0m[0;90m => [0m[0;32m'ReflectionObject'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m124[0m[0;90m => [0m[0;32m'ReflectionProperty'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m125[0m[0;90m => [0m[0;32m'ReflectionClassConstant'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m126[0m[0;90m => [0m[0;32m'ReflectionExtension'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m127[0m[0;90m => [0m[0;32m'ReflectionZendExtension'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m128[0m[0;90m => [0m[0;32m'ReflectionReference'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m129[0m[0;90m => [0m[0;32m'__PHP_Incomplete_Class'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m130[0m[0;90m => [0m[0;32m'php_user_filter'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m131[0m[0;90m => [0m[0;32m'Directory'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m132[0m[0;90m => [0m[0;32m'AssertionError'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m133[0m[0;90m => [0m[0;32m'SimpleXMLElement'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m134[0m[0;90m => [0m[0;32m'SimpleXMLIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m135[0m[0;90m => [0m[0;32m'PharException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m136[0m[0;90m => [0m[0;32m'Phar'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m137[0m[0;90m => [0m[0;32m'PharData'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m138[0m[0;90m => [0m[0;32m'PharFileInfo'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m139[0m[0;90m => [0m[0;32m'XMLReader'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m140[0m[0;90m => [0m[0;32m'XMLWriter'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m141[0m[0;90m => [0m[0;32m'GmagickException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m142[0m[0;90m => [0m[0;32m'GmagickPixelException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m143[0m[0;90m => [0m[0;32m'Gmagick'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m144[0m[0;90m => [0m[0;32m'GmagickDraw'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m145[0m[0;90m => [0m[0;32m'GmagickPixel'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m146[0m[0;90m => [0m[0;32m'Collator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m147[0m[0;90m => [0m[0;32m'NumberFormatter'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m148[0m[0;90m => [0m[0;32m'Normalizer'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m149[0m[0;90m => [0m[0;32m'Locale'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m150[0m[0;90m => [0m[0;32m'MessageFormatter'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m151[0m[0;90m => [0m[0;32m'IntlDateFormatter'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m152[0m[0;90m => [0m[0;32m'ResourceBundle'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m153[0m[0;90m => [0m[0;32m'Transliterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m154[0m[0;90m => [0m[0;32m'IntlTimeZone'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m155[0m[0;90m => [0m[0;32m'IntlCalendar'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m156[0m[0;90m => [0m[0;32m'IntlGregorianCalendar'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m157[0m[0;90m => [0m[0;32m'Spoofchecker'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m158[0m[0;90m => [0m[0;32m'IntlException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m159[0m[0;90m => [0m[0;32m'IntlIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m160[0m[0;90m => [0m[0;32m'IntlBreakIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m161[0m[0;90m => [0m[0;32m'IntlRuleBasedBreakIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m162[0m[0;90m => [0m[0;32m'IntlCodePointBreakIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m163[0m[0;90m => [0m[0;32m'IntlPartsIterator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m164[0m[0;90m => [0m[0;32m'UConverter'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m165[0m[0;90m => [0m[0;32m'IntlChar'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m166[0m[0;90m => [0m[0;32m'Saxon\SaxonProcessor'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m167[0m[0;90m => [0m[0;32m'Saxon\XSLTProcessor'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m168[0m[0;90m => [0m[0;32m'Saxon\XQueryProcessor'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m169[0m[0;90m => [0m[0;32m'Saxon\XPathProcessor'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m170[0m[0;90m => [0m[0;32m'Saxon\SchemaValidator'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m171[0m[0;90m => [0m[0;32m'Saxon\XdmValue'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m172[0m[0;90m => [0m[0;32m'Saxon\XdmItem'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m173[0m[0;90m => [0m[0;32m'Saxon\XdmNode'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m174[0m[0;90m => [0m[0;32m'Saxon\XdmAtomicValue'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m175[0m[0;90m => [0m[0;32m'SoapClient'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m176[0m[0;90m => [0m[0;32m'SoapVar'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m177[0m[0;90m => [0m[0;32m'SoapServer'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m178[0m[0;90m => [0m[0;32m'SoapFault'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m179[0m[0;90m => [0m[0;32m'SoapParam'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m180[0m[0;90m => [0m[0;32m'SoapHeader'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m181[0m[0;90m => [0m[0;32m'SodiumException'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m182[0m[0;90m => [0m[0;32m'XSLTProcessor'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m183[0m[0;90m => [0m[0;32m'ZipArchive'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m184[0m[0;90m => [0m[0;32m'ComposerAutoloaderInite2860f0462a3b3d19345b292a553c331'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m185[0m[0;90m => [0m[0;32m'Composer\Autoload\ClassLoader'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m186[0m[0;90m => [0m[0;32m'Composer\Autoload\ComposerStaticInite2860f0462a3b3d19345b292a553c331'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m187[0m[0;90m => [0m[0;32m'Laminas\ZendFrameworkBridge\Autoloader'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m188[0m[0;90m => [0m[0;32m'Laminas\ZendFrameworkBridge\RewriteRules'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m189[0m[0;90m => [0m[0;32m'Cake\Chronos\Chronos'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m190[0m[0;90m => [0m[0;32m'carbon\mutabledatetime'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m191[0m[0;90m => [0m[0;32m'Cake\Utility\Inflector'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m192[0m[0;90m => [0m[0;32m'PHPUnit\TextUI\Command'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m193[0m[0;90m => [0m[0;32m'PHPUnit\Util\Getopt'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m194[0m[0;90m => [0m[0;32m'PHPUnit\Util\Configuration'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m195[0m[0;90m => [0m[0;32m'PHPUnit\Util\Xml'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m196[0m[0;90m => [0m[0;32m'PHPUnit\TextUI\ResultPrinter'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m197[0m[0;90m => [0m[0;32m'PHPUnit\Util\Printer'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m198[0m[0;90m => [0m[0;32m'PHPUnit\Util\FileLoader'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m199[0m[0;90m => [0m[0;32m'Cake\Routing\Router'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m200[0m[0;90m => [0m[0;32m'Cake\Routing\RouteCollection'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m201[0m[0;90m => [0m[0;32m'Cake\Core\Configure'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m202[0m[0;90m => [0m[0;32m'Cake\Core\Configure\Engine\PhpConfig'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m203[0m[0;90m => [0m[0;32m'Cake\Utility\Hash'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m204[0m[0;90m => [0m[0;32m'Cake\Error\ConsoleErrorHandler'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m205[0m[0;90m => [0m[0;32m'Cake\Error\BaseErrorHandler'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m206[0m[0;90m => [0m[0;32m'Cake\Console\ConsoleOutput'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m207[0m[0;90m => [0m[0;32m'Cake\Cache\Cache'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m208[0m[0;90m => [0m[0;32m'Cake\Datasource\ConnectionManager'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m209[0m[0;90m => [0m[0;32m'Cake\Mailer\TransportFactory'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m210[0m[0;90m => [0m[0;32m'Cake\Mailer\Email'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m211[0m[0;90m => [0m[0;32m'Cake\Mailer\Mailer'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m212[0m[0;90m => [0m[0;32m'Cake\Log\Log'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m213[0m[0;90m => [0m[0;32m'Cake\Utility\Security'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m214[0m[0;90m => [0m[0;32m'Cake\Http\ServerRequest'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m215[0m[0;90m => [0m[0;32m'Cake\Database\TypeFactory'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m216[0m[0;90m => [0m[0;32m'cake\database\type'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m217[0m[0;90m => [0m[0;32m'Cake\Database\Type\TimeType'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m218[0m[0;90m => [0m[0;32m'Cake\Database\Type\DateTimeType'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m219[0m[0;90m => [0m[0;32m'Cake\Database\Type\BaseType'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m220[0m[0;90m => [0m[0;32m'Cake\I18n\FrozenTime'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m221[0m[0;90m => [0m[0;32m'Cake\Database\Type\DateType'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m222[0m[0;90m => [0m[0;32m'Cake\I18n\FrozenDate'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m223[0m[0;90m => [0m[0;32m'Cake\Chronos\Date'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m224[0m[0;90m => [0m[0;32m'PHPUnit\Framework\TestSuite'[0m
  [0;90m][0m
[0;90m}[0m</div>
                            <div class="cake-debug">[0;90m[[0m
  [0;32m'listGroups'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'listSuites'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'listTests'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'listTestsXml'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'loader'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
  [0;32m'useDefaultConfiguration'[0m[0;90m => [0m[1;33mtrue[0m[0;90m,[0m
  [0;32m'loadedExtensions'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m[0;90m,[0m
  [0;32m'notLoadedExtensions'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m[0;90m,[0m
  [0;32m'configuration'[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Util\Configuration[0m[0;90m) id:[0m[1;34m0[0m[0;90m {[0m
    [0;35mprivate[0m [0;39minstances[0m[0;90m => [0m[0;90m[[0m
      [0;32m'/var/www/html/phpunit.xml.dist'[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Util\Configuration[0m[0;90m) id:[0m[1;34m0[0m[0;90m {}[0m
    [0;90m][0m
    [0;35mprivate[0m [0;39mdocument[0m[0;90m => [0m[0;90mobject([0m[0;36mDOMDocument[0m[0;90m) id:[0m[1;34m1[0m[0;90m {[0m[0;90m}[0m
    [0;35mprivate[0m [0;39mxpath[0m[0;90m => [0m[0;90mobject([0m[0;36mDOMXPath[0m[0;90m) id:[0m[1;34m2[0m[0;90m {[0m[0;90m}[0m
    [0;35mprivate[0m [0;39mfilename[0m[0;90m => [0m[0;32m'/var/www/html/phpunit.xml.dist'[0m
    [0;35mprivate[0m [0;39merrors[0m[0;90m => [0m[0;90m[[0m[0;90m][0m
  [0;90m}[0m[0;90m,[0m
  [0;32m'printer'[0m[0;90m => [0m[0;32m'PHPUnit\Util\Log\TeamCity'[0m[0;90m,[0m
  [0;32m'testSuffixes'[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'Test.php'[0m[0;90m,[0m
    [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'.phpt'[0m
  [0;90m][0m[0;90m,[0m
  [0;32m'debug'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'filter'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'listeners'[0m[0;90m => [0m[0;90m[[0m
    [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\TestSuite\Fixture\FixtureInjector[0m[0;90m) id:[0m[1;34m3[0m[0;90m {[0m
      [0;35mprotected[0m [0;39m_fixtureManager[0m[0;90m => [0m[0;90mobject([0m[0;36mCake\TestSuite\Fixture\FixtureManager[0m[0;90m) id:[0m[1;34m4[0m[0;90m {[0m[0;90m}[0m
      [0;35mprotected[0m [0;39m_first[0m[0;90m => [0m[0;90mobject([0m[0;36mPHPUnit\Framework\TestSuite[0m[0;90m) id:[0m[1;34m5[0m[0;90m {[0m[0;90m}[0m
    [0;90m}[0m
  [0;90m][0m[0;90m,[0m
  [0;32m'bootstrap'[0m[0;90m => [0m[0;32m'/var/www/html/./tests/bootstrap.php'[0m[0;90m,[0m
  [0;32m'colors'[0m[0;90m => [0m[0;32m'auto'[0m[0;90m,[0m
  [0;32m'processIsolation'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'stopOnFailure'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'testdoxGroups'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m[0;90m,[0m
  [0;32m'testdoxExcludeGroups'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m[0;90m,[0m
  [0;32m'addUncoveredFilesFromWhitelist'[0m[0;90m => [0m[1;33mtrue[0m[0;90m,[0m
  [0;32m'backupGlobals'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
  [0;32m'backupStaticAttributes'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
  [0;32m'beStrictAboutChangesToGlobalState'[0m[0;90m => [0m[1;33mnull[0m[0;90m,[0m
  [0;32m'beStrictAboutResourceUsageDuringSmallTests'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'cacheResult'[0m[0;90m => [0m[1;33mtrue[0m[0;90m,[0m
  [0;32m'cacheTokens'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'columns'[0m[0;90m => [0m[0;35m(int)[0m [1;34m80[0m[0;90m,[0m
  [0;32m'convertDeprecationsToExceptions'[0m[0;90m => [0m[1;33mtrue[0m[0;90m,[0m
  [0;32m'convertErrorsToExceptions'[0m[0;90m => [0m[1;33mtrue[0m[0;90m,[0m
  [0;32m'convertNoticesToExceptions'[0m[0;90m => [0m[1;33mtrue[0m[0;90m,[0m
  [0;32m'convertWarningsToExceptions'[0m[0;90m => [0m[1;33mtrue[0m[0;90m,[0m
  [0;32m'crap4jThreshold'[0m[0;90m => [0m[0;35m(int)[0m [1;34m30[0m[0;90m,[0m
  [0;32m'disallowTestOutput'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'disallowTodoAnnotatedTests'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'defaultTimeLimit'[0m[0;90m => [0m[0;35m(int)[0m [1;34m0[0m[0;90m,[0m
  [0;32m'enforceTimeLimit'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'excludeGroups'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m[0;90m,[0m
  [0;32m'executionOrder'[0m[0;90m => [0m[0;35m(int)[0m [1;34m0[0m[0;90m,[0m
  [0;32m'executionOrderDefects'[0m[0;90m => [0m[0;35m(int)[0m [1;34m0[0m[0;90m,[0m
  [0;32m'failOnRisky'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'failOnWarning'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'groups'[0m[0;90m => [0m[0;90m[[0m[0;90m][0m[0;90m,[0m
  [0;32m'noInteraction'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'processUncoveredFilesFromWhitelist'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'randomOrderSeed'[0m[0;90m => [0m[0;35m(int)[0m [1;34m1637855854[0m[0;90m,[0m
  [0;32m'registerMockObjectsFromTestArgumentsRecursively'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'repeat'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'reportHighLowerBound'[0m[0;90m => [0m[0;35m(int)[0m [1;34m90[0m[0;90m,[0m
  [0;32m'reportLowUpperBound'[0m[0;90m => [0m[0;35m(int)[0m [1;34m50[0m[0;90m,[0m
  [0;32m'reportUselessTests'[0m[0;90m => [0m[1;33mtrue[0m[0;90m,[0m
  [0;32m'reverseList'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'resolveDependencies'[0m[0;90m => [0m[1;33mtrue[0m[0;90m,[0m
  [0;32m'stopOnError'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'stopOnIncomplete'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'stopOnRisky'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'stopOnSkipped'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'stopOnWarning'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'stopOnDefect'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'strictCoverage'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'timeoutForLargeTests'[0m[0;90m => [0m[0;35m(int)[0m [1;34m60[0m[0;90m,[0m
  [0;32m'timeoutForMediumTests'[0m[0;90m => [0m[0;35m(int)[0m [1;34m10[0m[0;90m,[0m
  [0;32m'timeoutForSmallTests'[0m[0;90m => [0m[0;35m(int)[0m [1;34m1[0m[0;90m,[0m
  [0;32m'verbose'[0m[0;90m => [0m[1;33mfalse[0m[0;90m,[0m
  [0;32m'cacheResultFile'[0m[0;90m => [0m[0;32m'/var/www/html'[0m
[0;90m][0m</div>
                            <div class="cake-debug">[0;90m[[0m[0;90m][0m</div>
                            <div class="cake-debug">[1;33mtrue[0m</div>
                    </div>
    </div>
    <div id="stack-frame-34" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/phpunit/phpunit/src/TextUI/Command.php&amp;line=195">ROOT/vendor/phpunit/phpunit/src/TextUI/Command.php</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-34">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="191"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">*&nbsp;@</span><span style="color: #0000BB">throws&nbsp;</span><span style="color: #007700">\</span><span style="color: #0000BB">PHPUnit</span><span style="color: #007700">\</span><span style="color: #0000BB">Framework</span><span style="color: #007700">\</span><span style="color: #0000BB">Exception</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="192"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">*/</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="193"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">public&nbsp;static&nbsp;function&nbsp;</span><span style="color: #0000BB">main</span><span style="color: #007700">(</span><span style="color: #0000BB">bool&nbsp;$exit&nbsp;</span><span style="color: #007700">=&nbsp;</span><span style="color: #0000BB">true</span><span style="color: #007700">):&nbsp;</span><span style="color: #0000BB">int</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="194"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">{</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="195"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">return&nbsp;(new&nbsp;static)-&gt;</span><span style="color: #0000BB">run</span><span style="color: #007700">(</span><span style="color: #0000BB">$_SERVER</span><span style="color: #007700">[</span><span style="color: #DD0000">'argv'</span><span style="color: #007700">],&nbsp;</span><span style="color: #0000BB">$exit</span><span style="color: #007700">);</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="196"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">}</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="197"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="198"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #FF8000">/**</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="199"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span style="color: #007700">*&nbsp;@</span><span style="color: #0000BB">throws&nbsp;Exception</span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-34" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">[0;90m[[0m
  [0;35m(int)[0m [1;34m0[0m[0;90m => [0m[0;32m'/var/www/html/vendor/phpunit/phpunit/phpunit'[0m[0;90m,[0m
  [0;35m(int)[0m [1;34m1[0m[0;90m => [0m[0;32m'--configuration'[0m[0;90m,[0m
  [0;35m(int)[0m [1;34m2[0m[0;90m => [0m[0;32m'/var/www/html/phpunit.xml.dist'[0m[0;90m,[0m
  [0;35m(int)[0m [1;34m3[0m[0;90m => [0m[0;32m'--teamcity'[0m
[0;90m][0m</div>
                            <div class="cake-debug">[1;33mtrue[0m</div>
                    </div>
    </div>
    <div id="stack-frame-35" style="display:none;" class="stack-details">
        <div class="stack-frame-header">
            <span class="stack-frame-file">
                                    <a href="phpstorm://open?file=/var/www/html/vendor/phpunit/phpunit/phpunit&amp;line=61">ROOT/vendor/phpunit/phpunit/phpunit</a>                            </span>
            <a href="#" class="toggle-link stack-frame-args" data-target="stack-args-35">Toggle Arguments</a>
        </div>

        <table class="code-excerpt" cellspacing="0" cellpadding="0">
                            <tr>
                <td class="excerpt-number" data-number="57"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span><span style="color: #007700">unset(</span><span style="color: #0000BB">$options</span><span style="color: #007700">);</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="58"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="59"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span><span style="color: #007700">require&nbsp;</span><span style="color: #0000BB">PHPUNIT_COMPOSER_INSTALL</span><span style="color: #007700">;</span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="60"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="61"></td>
                <td class="excerpt-line"><span class="code-highlight"><code><span style="color: #000000"><span style="color: #0000BB">PHPUnit</span><span style="color: #007700">\</span><span style="color: #0000BB">TextUI</span><span style="color: #007700">\</span><span style="color: #0000BB">Command</span><span style="color: #007700">::</span><span style="color: #0000BB">main</span><span style="color: #007700">();</span></span></code></span></td>
            </tr>
                    <tr>
                <td class="excerpt-number" data-number="62"></td>
                <td class="excerpt-line"><code><span style="color: #000000"><span style="color: #0000BB"></span></span></code></td>
            </tr>
                </table>

        <div id="stack-args-35" class="cake-debug" style="display: none;">
            <h4>Arguments</h4>
                            <div class="cake-debug">No arguments</div>
                    </div>
    </div>

            <div class="error-suggestion">

Xdebug: user triggered in /var/www/html/templates/Error/error400.php on line 39

Call Stack:
    0.0003     397592   1. {main}() /var/www/html/vendor/phpunit/phpunit/phpunit:0
    0.0080    1820880   2. PHPUnit\TextUI\Command::main() /var/www/html/vendor/phpunit/phpunit/phpunit:61
    0.0080    1821008   3. PHPUnit\TextUI\Command->run() /var/www/html/vendor/phpunit/phpunit/src/TextUI/Command.php:195
    0.1838    9700896   4. PHPUnit\TextUI\TestRunner->doRun() /var/www/html/vendor/phpunit/phpunit/src/TextUI/Command.php:236
    0.2025   10522048   5. PHPUnit\Framework\TestSuite->run() /var/www/html/vendor/phpunit/phpunit/src/TextUI/TestRunner.php:656
    0.2056   10522240   6. PHPUnit\Framework\TestSuite->run() /var/www/html/vendor/phpunit/phpunit/src/Framework/TestSuite.php:627
   29.1993   35696424   7. PHPUnit\Framework\TestSuite->run() /var/www/html/vendor/phpunit/phpunit/src/Framework/TestSuite.php:627
   30.0246   38391176   8. App\Test\TestCase\Controller\WikiControllerTest->run() /var/www/html/vendor/phpunit/phpunit/src/Framework/TestSuite.php:627
   30.0246   38391176   9. PHPUnit\Framework\TestResult->run() /var/www/html/vendor/phpunit/phpunit/src/Framework/TestCase.php:819
   30.1973   38391304  10. App\Test\TestCase\Controller\WikiControllerTest->runBare() /var/www/html/vendor/phpunit/phpunit/src/Framework/TestResult.php:703
   30.1993   38329520  11. App\Test\TestCase\Controller\WikiControllerTest->runTest() /var/www/html/vendor/phpunit/phpunit/src/Framework/TestCase.php:1091
   30.1993   38329520  12. App\Test\TestCase\Controller\WikiControllerTest->testNotAuthorizedReader() /var/www/html/vendor/phpunit/phpunit/src/Framework/TestCase.php:1471
   30.2128   38470416  13. App\Test\TestCase\Controller\WikiControllerTest->get() /var/www/html/tests/TestCase/Controller/WikiControllerTest.php:93
   30.2128   38470416  14. App\Test\TestCase\Controller\WikiControllerTest->_sendRequest() /var/www/html/vendor/cakephp/cakephp/src/TestSuite/IntegrationTestTrait.php:385
   30.2131   38474328  15. Cake\TestSuite\MiddlewareDispatcher->execute() /var/www/html/vendor/cakephp/cakephp/src/TestSuite/IntegrationTestTrait.php:499
   30.2136   38481880  16. Cake\Http\Server->run() /var/www/html/vendor/cakephp/cakephp/src/TestSuite/MiddlewareDispatcher.php:190
   30.2146   38490216  17. Cake\Http\Runner->run() /var/www/html/vendor/cakephp/cakephp/src/Http/Server.php:90
   30.2146   38490216  18. Cake\Http\Runner->handle() /var/www/html/vendor/cakephp/cakephp/src/Http/Runner.php:58
   30.2146   38490216  19. Cake\Error\Middleware\ErrorHandlerMiddleware->process() /var/www/html/vendor/cakephp/cakephp/src/Http/Runner.php:73
   30.2294   38682032  20. Cake\Error\Middleware\ErrorHandlerMiddleware->handleException() /var/www/html/vendor/cakephp/cakephp/src/Error/Middleware/ErrorHandlerMiddleware.php:125
   30.2338   38720840  21. Cake\Error\ExceptionRenderer->render() /var/www/html/vendor/cakephp/cakephp/src/Error/Middleware/ErrorHandlerMiddleware.php:142
   30.2340   38739288  22. Cake\Error\ExceptionRenderer->_outputMessage() /var/www/html/vendor/cakephp/cakephp/src/Error/ExceptionRenderer.php:248
   30.2340   38739288  23. App\Controller\ErrorController->render() /var/www/html/vendor/cakephp/cakephp/src/Error/ExceptionRenderer.php:369
   30.2352   38782752  24. App\View\AppView->render() /var/www/html/vendor/cakephp/cakephp/src/Controller/Controller.php:696
   30.2353   38783288  25. App\View\AppView->_render() /var/www/html/vendor/cakephp/cakephp/src/View/View.php:764
   30.2353   38783288  26. App\View\AppView->_evaluate() /var/www/html/vendor/cakephp/cakephp/src/View/View.php:1134
   30.2354   38808624  27. include('/var/www/html/templates/Error/error400.php') /var/www/html/vendor/cakephp/cakephp/src/View/View.php:1176
   30.2356   38825888  28. xdebug_print_function_stack() /var/www/html/templates/Error/error400.php:39

            </div>

                        <p class="customize">
                If you want to customize this error message, create
                <em>templates/Error/error400.ctp</em>
            </p>
                    </div>
    </div>

    <script type="text/javascript">
        function bindEvent(selector, eventName, listener) {
            var els = document.querySelectorAll(selector);
            for (var i = 0, len = els.length; i < len; i++) {
                els[i].addEventListener(eventName, listener, false);
            }
        }

        function toggleElement(el) {
            if (el.style.display === 'none') {
                el.style.display = 'block';
            } else {
                el.style.display = 'none';
            }
        }

        function each(els, cb) {
            var i, len;
            for (i = 0, len = els.length; i < len; i++) {
                cb(els[i], i);
            }
        }

        window.addEventListener('load', function() {
            bindEvent('.stack-frame-args', 'click', function(event) {
                var target = this.dataset['target'];
                var el = document.getElementById(target);
                toggleElement(el);
                event.preventDefault();
            });

            var details = document.querySelectorAll('.stack-details');
            var frames = document.querySelectorAll('.stack-frame');
            bindEvent('.stack-frame a', 'click', function(event) {
                each(frames, function(el) {
                    el.classList.remove('active');
                });
                this.parentNode.classList.add('active');

                each(details, function(el) {
                    el.style.display = 'none';
                });

                var target = document.getElementById(this.dataset['target']);
                toggleElement(target);
                event.preventDefault();
            });

            bindEvent('.toggle-vendor-frames', 'click', function(event) {
                each(frames, function(el) {
                    if (el.classList.contains('vendor-frame')) {
                        toggleElement(el);
                    }
                });
                event.preventDefault();
            });

            bindEvent('.header-title a', 'click', function(event) {
                event.preventDefault();
                var text = '';
                each(this.parentNode.childNodes, function(el) {
                    if (el.nodeName !== 'A') {
                        text += el.textContent.trim();
                    }
                });

                // Use execCommand(copy) as it has the widest support.
                var textArea = document.createElement("textarea");
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                var el = this;
                try {
                    document.execCommand('copy');

                    // Show a success icon and then revert
                    var original = el.innerText;
                    el.innerText = '\ud83c\udf70';
                    setTimeout(function () {
                        el.innerText =  original;
                    }, 1000);
                } catch (err) {
                    alert('Unable to update clipboard ' + err);
                }
                document.body.removeChild(textArea);
                this.parentNode.parentNode.scrollIntoView(true);
            });
        });
    </script>
</body>
</html>
