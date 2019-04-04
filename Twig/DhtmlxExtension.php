<?php

namespace Recode\DhtmlxBundle\Twig;

use Recode\DhtmlxBundle\Gantt\AbstractGantt;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DhtmlxExtension extends AbstractExtension
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * DhtmlxExtension constructor.
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @return array|\Twig\TwigFunction[]|void
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('gantt_js', [$this, 'renderGanttJs'], ['is_safe' => ['html']]),
            new TwigFunction('gantt_html', [$this, 'renderGanttHtml'], ['is_safe' => ['html']])
        ];
    }

    /**
     * @param AbstractGantt $gantt
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renderGanttJs(AbstractGantt $gantt)
    {
        return $this->twig->render("@Dhtmlx/gantt.js.twig", [
            'gantt' => $gantt
        ]);
    }

    /**
     * @param AbstractGantt $gantt
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renderGanttHtml(AbstractGantt $gantt)
    {
        return $this->twig->render("@Dhtmlx/gantt.html.twig", [
            'gantt' => $gantt
        ]);
    }

    public function getName()
    {
        return 'recode_dhtmlx_extension';
    }


}