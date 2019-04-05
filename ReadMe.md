# Recode bundle notice

 Based on [jsh11](https://github.com/jsh11/dhtmlx-bundle/blob/master/Resources/doc/index.md)   work's it's just an adaptation for symfony 4 it's work like it. 
 
 
 # Installation
    composer require recode/dhtmlx-bundle
 
 ### Use it
 First you need to create an entity and make a One-To-Many, Self-referencing relation you can check the dock [here](https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/association-mapping.html#one-to-many-self-referencing)
 
 Exemple with an entity user
    
    <?php
    
    namespace App\Entity;
    
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\ORM\Mapping as ORM;
    
    /**
     * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
     */
    class User
    {
        /**
         * @ORM\Id()
         * @ORM\GeneratedValue()
         * @ORM\Column(type="integer")
         */
        private $id;
    
        /**
         * @ORM\Column(type="string", length=255)
         */
        private $name;
    
        /**
         * @ORM\Column(type="datetime")
         */
        private $startAt;
    
        /**
         * @ORM\Column(type="float")
         */
        private $duration;
    
        /**
         * @ORM\Column(type="integer")
         */
        private $advanced;
    
        /**
         * @ORM\OneToMany(targetEntity="User", mappedBy="parent")
         */
        private $children;
    
        /**
         * @ORM\ManyToOne(targetEntity="User", inversedBy="children")
         * @ORM\JoinColumn(name="parent_id",referencedColumnName="id")
         */
        private $parent;
    
        public function __construct()
        {
            $this->users = new ArrayCollection();
            $this->children = new ArrayCollection();
        }
    
        public function getId(): ?int
        {
            return $this->id;
        }
    
        /**
         * @param mixed $id
         */
        public function setId($id): void
        {
            $this->id = $id;
        }
    
        public function getName(): ?string
        {
            return $this->name;
        }
    
        public function setName(string $name): self
        {
            $this->name = $name;
    
            return $this;
        }
    
        public function getStartAt(): ?\DateTimeInterface
        {
            return $this->startAt;
        }
    
        public function setStartAt(\DateTimeInterface $startAt): self
        {
            $this->startAt = $startAt;
    
            return $this;
        }
    
        public function getDuration(): ?float
        {
            return $this->duration;
        }
    
        public function setDuration(float $duration): self
        {
            $this->duration = $duration;
    
            return $this;
        }
    
        public function getAdvanced(): ?int
        {
            return $this->advanced;
        }
    
        public function setAdvanced(int $advanced): self
        {
            $this->advanced = $advanced;
    
            return $this;
        }
    
        /**
         * @return ArrayCollection
         */
        public function getUsers(): ArrayCollection
        {
            return $this->users;
        }
    
        /**
         * @param ArrayCollection $users
         * @return User
         */
        public function setUsers(ArrayCollection $users): User
        {
            $this->users = $users;
            return $this;
        }
    
        /**
         * @return mixed
         */
        public function getChildren()
        {
            return $this->children;
        }
    
        /**
         * @param mixed $children
         * @return User
         */
        public function setChildren($children)
        {
            $this->children = $children;
            return $this;
        }
    
        /**
         * @return mixed
         */
        public function getParent()
        {
            return $this->parent;
        }
    
        /**
         * @param mixed $parent
         * @return User
         */
        public function setParent($parent)
        {
            $this->parent = $parent;
            return $this;
        }        
    }
 
 
 **And you need add the function __ToString() at the end of your entity**
 
     public function __toString()
         {
             return $this->name;
         }

----
  
## Next step

You need to create a form check the symfony doc [here](https://symfony.com/doc/current/forms.html)

---

## Create a class

    <?php
    
    namespace App\Gantt;
    
    use App\Entity\User;
    use Recode\DhtmlxBundle\Gantt\AbstractGantt;
    
    class UserGantt extends AbstractGantt
    {
    
        public function configure()
        {
            $this->setAjax([
                'route_list' => 'user_index',
                'route_new' => 'user_new',
                'route_edit' => 'user_edit',
                'route_delete' => 'user_delete'
            ]);
            $this->setConfig([
                'date_grid' => "%d.%m.%Y",
                'step' => 1,
                'scale_unit' => 'day',
            ]);
            $this->setMapping([
                'id' => 'id',
                'text' => 'name',
                'start_date' => 'startAt',
                'duration' => 'duration',
                'progress' => 'advanced',
                'parent' => 'parent'
    
            ]);
    
            return $this;
        }
    
        public function getEntity()
        {
            return User::class;
        }
    
        public function getName()
        {
            return "user_gantt";
    
        }
    }

---

## Create a controller
    <?php
    
    namespace App\Controller;
    
    use App\Entity\User;
    use App\Form\UserType;
    use App\Gantt\UserGantt;
    use App\Repository\UserRepository;
    use Recode\DhtmlxBundle\Factory\GanttFactory;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;
    
    class UserController extends AbstractController
    {
        /**
         * @var GanttFactory
         */
        private $ganttFactory;
        /**
         * @var UserRepository
         */
        private $repository;
    
        /**
         * UserController constructor.
         * @param GanttFactory $ganttFactory
         */
        public function __construct(GanttFactory $ganttFactory, UserRepository $repository)
        {
            $this->ganttFactory = $ganttFactory;
            $this->repository = $repository;
        }
    
        /**
         * @Route("/user", name="user_index", options={"expose" = true})
         * @param Request $request
         * @param UserRepository $repository
         * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
         * @throws \Exception
         */
        public function index(Request $request)
        {
            $gantt = $this->ganttFactory->create(UserGantt::class);
            $gantt->handleRequest($request);
    
            if ($gantt->isSubmitted()) {
                return $gantt->getResponse();
            }
    
            return $this->render('user/index.html.twig', [
                'gantt' => $gantt,
            ]);
        }
    
    
    
        /**
         * Create a new user
         *
         * @Route("/new", name="user_new", options={"expose" = true}, methods={"GET", "POST"})
         * @param Request $request
         *
         */
        public function newUser(Request $request)
        {
            $user = new User();
            $form = $this->createForm(UserType::class, $user, [
                'action' => $this->generateUrl('user_new'),
                'method' => 'POST'
            ]);
            $form->handleRequest($request);
    
    
    
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                return new JsonResponse([
                    'type' => 'success'
                ]);
            }
            return $this->render('user/form.html.twig', array(
                'user' => $user,
                'form' => $form->createView(),
            ));
        }
    
    
    
        /**
         * Displays a form to existing user
         *
         * @Route("/{id}/edit", name="user_edit", options={"expose" = true}, methods={"GET", "POST"})
         * @param Request $request
         * @param User $user
         */
        public function editUser(Request $request, User $user)
        {
            $form = $this->createForm(UserType::class, $user, [
                'action' => $this->generateUrl('user_edit', ['id' => $user->getId()]),
                'method' => 'POST'
            ]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                return new JsonResponse([
                    'type' => 'success'
                ]);
            }
            return $this->render('user/form.html.twig', array(
                'user' => $user,
                'form' => $form->createView(),
            ));
        }
    
        /**
         * Delete a user
         *
         * @param Request $request
         * @param User $user
         * @return JsonResponse
         *
         * @Route("/{id}/delete", name="user_delete", options={"expose" = true}, methods={"GET"})
         */
        public function deleteUser(Request $request, User $user)
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
            return new JsonResponse([
                'type' => 'success'
            ]);
        }
    }
    
---

### Create a twig template 
    {% extends "base.html.twig" %}
    
    {% block  stylesheets %}
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
              integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="http://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css"
              type="text/css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.css">
        <style>
            body, html {
                height: 100%;
                width: 100%;
                margin: 0;
                padding: 0;
                overflow: hidden;
            }
        </style>
    {% endblock %}
    
    {% block  body %}
        <div class="container mt-4 text-center">
            <label><input type="radio"  name="scale" value="day" checked/><i class="material-icons">Day scale</i></label>
            <label><input type="radio"  name="scale" value="week"/><i class="material-icons">Week scale</i></label>
            <label><input type="radio"  name="scale" value="month"/><i class="material-icons">Month scale</i></label>
            <label><input type="radio"  name="scale" value="year"/><i class="material-icons">Year scale</i></label>
        </div>
        {{ gantt_html(gantt) }}
    {% endblock %}
    
    {% block  javascripts %}
        <script
                src="http://code.jquery.com/jquery-3.3.1.min.js"
                integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
                crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
                integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
                crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
                integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
                crossorigin="anonymous"></script>
        <script src="http://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.4/jquery-confirm.min.js"></script>
        <script src="{{ asset('bundles/fosjsrouting/js/router.min.js') }}"></script>
        <script src="{{ path('fos_js_routing_js', { callback: 'fos.Router.setData' }) }}"></script>
        <script>
    
            function setScaleConfig(level) {
                switch (level) {
                    case "day":
                        gantt.config.scale_unit = "day";
                        gantt.config.step = 1;
                        gantt.config.date_scale = "%d %M";
                        gantt.templates.date_scale = null;
    
                        gantt.config.scale_height = 27;
    
                        gantt.config.subscales = [];
                        break;
                    case "week":
                        var weekScaleTemplate = function (date) {
                            var dateToStr = gantt.date.date_to_str("%d %M");
                            var endDate = gantt.date.add(gantt.date.add(date, 1, "week"), -1, "day");
                            return dateToStr(date) + " - " + dateToStr(endDate);
                        };
    
                        gantt.config.scale_unit = "week";
                        gantt.config.step = 1;
                        gantt.templates.date_scale = weekScaleTemplate;
    
                        gantt.config.scale_height = 50;
    
                        gantt.config.subscales = [
                            {unit: "day", step: 1, date: "%D"}
                        ];
                        break;
                    case "month":
                        gantt.config.scale_unit = "month";
                        gantt.config.date_scale = "%F, %Y";
                        gantt.templates.date_scale = null;
    
                        gantt.config.scale_height = 50;
    
                        gantt.config.subscales = [
                            {unit: "day", step: 1, date: "%j, %D"}
                        ];
    
                        break;
                    case "year":
                        gantt.config.scale_unit = "year";
                        gantt.config.step = 1;
                        gantt.config.date_scale = "%Y";
                        gantt.templates.date_scale = null;
    
                        gantt.config.min_column_width = 50;
                        gantt.config.scale_height = 90;
    
                        gantt.config.subscales = [
                            {unit: "month", step: 1, date: "%M"}
                        ];
                        break;
                }
            }
    
    
    
            $(function () {
                setScaleConfig("year");
                {{ gantt_js(gantt) }}
    
                var els = document.querySelectorAll("input[name='scale']");
                for (var i = 0; i < els.length; i++) {
                    els[i].onclick = function(e){
                        e = e || window.event;
                        var el = e.target || e.srcElement;
                        var value = el.value;
                        setScaleConfig(value);
                        gantt.render();
                    };
                }
            });
        </script>
    
    {% endblock %}
    
As you can see, we can use methods of [dhtmlx library](https://docs.dhtmlx.com/gantt/index.html) with javascript for make what do you wants.

---

## You need fos-js-routing for use bundle
    composer require friendsofsymfony/jsrouting-bundle
    
And next do in the terminal
 
    php bin/console assets:install --symlink public

For more informations check the doc of symfony [fos-js-routing](https://symfony.com/doc/master/bundles/FOSJsRoutingBundle/installation.html)

## Solve the autowiring error

If you have an error with autowiring, you need to modified your services.yaml file and add 
    
    Recode\DhtmlxBundle\Factory\GanttFactory:
            autowire: true

