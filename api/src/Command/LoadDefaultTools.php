<?php

declare(strict_types=1);

namespace App\Command;

use App\Model\SimpleTool\SimpleTool;
use App\Model\ToolMetadata;
use App\Model\User;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class LoadDefaultTools extends Command
{
    protected static $defaultName = 'app:load-tools';

    /** @var MessageBusInterface $commandBus */
    private $commandBus;

    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var KernelInterface $kernel */
    private $kernel;

    public function __construct(MessageBusInterface $commandBus, EntityManagerInterface $entityManager, KernelInterface $kernel)
    {
        $this->commandBus = $commandBus;
        $this->entityManager = $entityManager;
        $this->kernel = $kernel;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Loads tools for specified user.')
            ->addArgument('username', InputArgument::OPTIONAL, 'username')
            ->setHelp('This command allows you to default tools');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user instanceof User) {
            throw new \Exception(sprintf('User with username %s not found.', $username));
        }

        // Delete all SimpleTools with User === null
        $simpleTools = $this->entityManager->getRepository(SimpleTool::class)->findAll();

        /** @var SimpleTool $simpleTool */
        foreach ($simpleTools as $simpleTool) {
            $this->entityManager->remove($simpleTool);
            $this->entityManager->flush();
        }

        $simpleTools = $this->entityManager->getRepository(SimpleTool::class)->findBy(['user' => $user]);
        /** @var SimpleTool $simpleTool */
        foreach ($simpleTools as $simpleTool) {
            if ($simpleTool->name() === 'Default') {
                $this->entityManager->remove($simpleTool);
                $this->entityManager->flush();
                $output->writeln(sprintf('Delete Default Tool %s', $simpleTool->tool()));
            }
        }

        $tools = ['T02', 'T08', 'T09A', 'T09B', 'T09C', 'T09D', 'T09E', 'T13A', 'T13B', 'T13C', 'T13E', 'T14A', 'T14B', 'T14C', 'T14D'];

        foreach ($tools as $tool) {
            $simpleTool = $this->tools($tool, $user);
            if (!$simpleTool instanceof SimpleTool) {
                continue;
            }
            $this->entityManager->persist($simpleTool);
            $this->entityManager->flush();
            $output->writeln(sprintf('Create Default Tool %s', $simpleTool->tool()));
        }
    }

    /**
     * @param string $tool
     * @param User $user
     * @return SimpleTool
     * @throws \Exception
     */
    public function tools(string $tool, User $user): ?SimpleTool
    {
        $simpleTool = SimpleTool::createWithParams(
            Uuid::uuid4()->toString(),
            $user,
            $tool,
            ToolMetadata::fromParams(
                'Default',
                '',
                true
            )
        );

        switch ($tool) {
            case 'T02':
                $simpleTool->setData([json_decode(
                    '{
                        "settings":{"variable":"x"},
                        "parameters":[
                            {"id":"w","max":10,"min":0,"value":0.045},
                            {"id":"L","max":1000,"min":0,"value":40},
                            {"id":"W","max":100,"min":0,"value":20},
                            {"id":"hi","max":100,"min":0,"value":35},
                            {"id":"Sy","max":0.5,"min":0,"value":0.085},
                            {"id":"K","max":10,"min":0.1,"value":1.83},
                            {"id":"t","max":100,"min":0,"value":1.5}
                        ],
                        "tool":"' . $tool . '"
                        }',
                    true
                )]);
                break;
            case 'T08':
                $simpleTool->setData([json_decode(
                    '{
                        "settings": {
                            "retardation": true,
                            "case": 1,
                            "infiltration": 2
                        },
                        "parameters": [
                            {
                             "id": "C0",
                              "max": 1000,
                              "min": 0,
                              "value": 100
                            },
                            {
                              "id": "x",
                              "max": 100,
                              "min": 0,
                              "value": 10
                            },
                            {
                              "id": "t",
                              "max": 500,
                              "min": 0,
                              "value": 365
                            },
                            {
                              "id": "K",
                              "max": 100,
                              "min": 0.01,
                              "value": 2.592
                            },
                            {
                              "id": "I",
                              "max": 0.01,
                              "min": 0,
                              "value": 0.002
                            },
                            {
                              "id": "ne",
                              "max": 0.5,
                              "min": 0,
                              "value": 0.23
                            },
                            {
                              "id": "rhoS",
                              "max": 3,
                              "min": 0,
                              "value": 2.65
                            },
                            {
                              "id": "alphaL",
                              "max": 10,
                              "min": 0.1,
                              "value": 0.923
                            },
                            {
                              "id": "Kd",
                              "max": 0.1,
                              "min": 0,
                              "value": 0
                            },
                            {
                              "id": "tau",
                              "max": 500,
                              "min": 0,
                              "value": 100
                            },
                            {
                              "id": "Corg",
                              "max": 0.1,
                              "min": 0,
                              "value": 0.001
                            },
                            {
                              "id": "Kow",
                              "max": 10,
                              "min": 0,
                              "value": 2.25
                            }
                          ],
                        "tool":"' . $tool . '"
                        }',
                    true
                )]);
                break;
            case 'T09A':
                $simpleTool->setData([json_decode(
                    '{
                        "parameters":[
                            {"id":"h","max":10,"min":0,"value":1},
                            {"id":"df","max":1.03,"min":0.9,"value":1},
                            {"id":"ds","max":1.03,"min":0.9,"value":1.025}
                        ],
                        "tool":"' . $tool . '"
                        }',
                    true
                )]);
                break;
            case 'T09B':
                $simpleTool->setData([json_decode(
                    '{
                        "parameters": [
                            {
                              "id": "b",
                              "max": 100,
                              "min": 1,
                              "value": 50
                            },
                            {
                              "id": "i",
                              "max": 0.01,
                              "min": 0,
                              "value": 0.001
                            },
                            {
                              "id": "df",
                              "max": 1.03,
                              "min": 0.9,
                              "value": 1
                            },
                            {
                              "id": "ds",
                              "max": 1.03,
                              "min": 0.9,
                              "value": 1.025
                            }
                        ],
                        "tool":"' . $tool . '"
                        }',
                    true
                )]);
                break;
            case 'T09C':
                $simpleTool->setData([json_decode(
                    '{
                            "parameters": [
                                {
                                  "id": "q",
                                  "max": 3000,
                                  "min": 1,
                                  "value": 2000
                                },
                                {
                                  "id": "k",
                                  "max": 100,
                                  "min": 1,
                                  "value": 50
                                },
                                {
                                  "id": "d",
                                  "max": 50,
                                  "min": 1,
                                  "value": 30
                                },
                                {
                                  "id": "df",
                                  "max": 1.03,
                                  "min": 0.9,
                                  "value": 1
                                },
                                {
                                  "id": "ds",
                                  "max": 1.03,
                                  "min": 0.9,
                                  "value": 1.025
                                }
                            ],
                            "tool":"' . $tool . '"
                        }',
                    true
                )]);
                break;
            case 'T09D':
                $simpleTool->setData([json_decode(
                    '{
                        "parameters": [
                            {
                              "id": "k",
                              "max": 100,
                              "min": 1,
                              "value": 50
                            },
                            {
                              "id": "b",
                              "max": 100,
                              "min": 10,
                              "value": 20
                            },
                            {
                              "id": "q",
                              "max": 10,
                              "min": 0.1,
                              "value": 1
                            },
                            {
                              "id": "Q",
                              "max": 10000,
                              "min": 0,
                              "value": 5000
                            },
                            {
                              "id": "xw",
                              "max": 5000,
                              "min": 1000,
                              "value": 2000
                            },
                            {
                              "id": "rhof",
                              "max": 1.03,
                              "min": 0.9,
                              "value": 1
                            },
                            {
                              "id": "rhos",
                              "max": 1.03,
                              "min": 0.9,
                              "value": 1.025
                            }
                          ],
                          "tool":"' . $tool . '",
                          "settings": {
                            "AqType": "unconfined"
                          }
                        }',
                    true
                )]);
                break;
            case 'T09E':
                $simpleTool->setData([json_decode(
                    '{
                            "parameters": [
                                {
                                  "id": "k",
                                  "max": 100,
                                  "min": 1,
                                  "value": 20
                                },
                                {
                                  "id": "z0",
                                  "max": 100,
                                  "min": 0,
                                  "value": 25
                                },
                                {
                                  "id": "l",
                                  "max": 10000,
                                  "min": 0,
                                  "value": 2000
                                },
                                {
                                  "id": "w",
                                  "max": 0.001,
                                  "min": 0,
                                  "value": 0.0001
                                },
                                {
                                  "id": "dz",
                                  "max": 2,
                                  "min": 0,
                                  "value": 1
                                },
                                {
                                  "id": "hi",
                                  "max": 10,
                                  "min": 0,
                                  "value": 2
                                },
                                {
                                  "id": "i",
                                  "max": 0.01,
                                  "min": 0,
                                  "value": 0.001
                                },
                                {
                                  "id": "df",
                                  "max": 1.005,
                                  "min": 1,
                                  "value": 1
                                },
                                {
                                  "id": "ds",
                                  "max": 1.03,
                                  "min": 1.02,
                                  "value": 1.025
                                }
                              ],
                            "settings": {
                                "method": "constFlux"
                            },
                            "tool":"' . $tool . '"
                        }',
                    true
                )]);
                break;
            case 'T13A':
                $simpleTool->setData([json_decode(
                    '{
                            "parameters": [
                                {
                                  "id": "W",
                                  "max": 0.01,
                                  "min": 0.001,
                                  "value": 0.009
                                },
                                {
                                  "id": "K",
                                  "max": 1000,
                                  "min": 0.1,
                                  "value": 10.1
                                },
                                {
                                  "id": "ne",
                                  "max": 0.5,
                                  "min": 0,
                                  "value": 0.35
                                },
                                {
                                  "id": "L",
                                  "max": 1000,
                                  "min": 0,
                                  "value": 500
                                },
                                {
                                  "id": "hL",
                                  "max": 10,
                                  "min": 0,
                                  "value": 2
                                },
                                {
                                  "id": "xi",
                                  "max": 1000,
                                  "min": 0,
                                  "value": 50
                                },
                                {
                                  "id": "xe",
                                  "max": 1000,
                                  "min": 1,
                                  "value": 500
                                }
                            ],
                            "tool":"' . $tool . '"
                        }',
                    true
                )]);
                break;
            case 'T13B':
                $simpleTool->setData([json_decode(
                    '{
                           "parameters": [
                                {
                                  "id": "W",
                                  "max": 0.01,
                                  "min": 0.001,
                                  "value": 0.00112
                                },
                                {
                                  "id": "K",
                                  "max": 1000,
                                  "min": 0.1,
                                  "value": 30.2
                                },
                                {
                                  "id": "L",
                                  "max": 1000,
                                  "min": 0,
                                  "value": 1000
                                },
                                {
                                  "id": "hL",
                                  "max": 10,
                                  "min": 0,
                                  "value": 2
                                },
                                {
                                  "id": "h0",
                                  "max": 10,
                                  "min": 0,
                                  "value": 5
                                },
                                {
                                  "id": "ne",
                                  "max": 0.5,
                                  "min": 0,
                                  "value": 0.35
                                },
                                {
                                  "id": "xi",
                                  "max": 1000,
                                  "min": 0,
                                  "value": 50
                                },
                                {
                                  "id": "xe",
                                  "max": 1000,
                                  "min": 1,
                                  "value": 200
                                }
                            ],
                            "tool":"' . $tool . '"
                        }',
                    true
                )]);
                break;
            case 'T13C':
                $simpleTool->setData([json_decode(
                    '{
                           "parameters": [
                                {
                                  "id": "W",
                                  "max": 0.01,
                                  "min": 0.001,
                                  "value": 0.009
                                },
                                {
                                  "id": "K",
                                  "max": 1000,
                                  "min": 0.1,
                                  "value": 10.1
                                },
                                {
                                  "id": "ne",
                                  "max": 0.5,
                                  "min": 0,
                                  "value": 0.35
                                },
                                {
                                  "id": "L",
                                  "max": 1000,
                                  "min": 0,
                                  "value": 500
                                },
                                {
                                  "id": "hL",
                                  "max": 10,
                                  "min": 0,
                                  "value": 1
                                },
                                {
                                  "id": "h0",
                                  "max": 10,
                                  "min": 0,
                                  "value": 10
                                },
                                {
                                  "id": "xi",
                                  "max": 1000,
                                  "min": 0,
                                  "value": 330
                                },
                                {
                                  "id": "xe",
                                  "max": 1000,
                                  "min": 1,
                                  "value": 500
                                }
                              ],
                            "tool":"' . $tool . '"
                        }',
                    true
                )]);
                break;
            case 'T13E':
                $simpleTool->setData([json_decode(
                    '{
                             "parameters": [
                                {
                                  "id": "Qw",
                                  "max": 10000,
                                  "min": 0,
                                  "value": 1300
                                },
                                {
                                  "id": "ne",
                                  "max": 0.5,
                                  "min": 0,
                                  "value": 0.35
                                },
                                {
                                  "id": "hL",
                                  "max": 20,
                                  "min": 0,
                                  "value": 6
                                },
                                {
                                  "id": "h0",
                                  "max": 20,
                                  "min": 0,
                                  "value": 10
                                },
                                {
                                  "id": "xi",
                                  "max": 1000,
                                  "min": 0,
                                  "value": 303
                                },
                                {
                                  "id": "x",
                                  "max": 1000,
                                  "min": 0,
                                  "value": 0.1
                                }
                              ],
                            "tool":"' . $tool . '"
                        }',
                    true
                )]);
                break;
            case 'T14A':
                $simpleTool->setData([json_decode(
                    '{
                          "parameters": [
                            {
                              "id": "Qw",
                              "max": 1000,
                              "min": 1,
                              "value": 150
                            },
                            {
                              "id": "t",
                              "max": 500,
                              "min": 1,
                              "value": 365
                            },
                            {
                              "id": "S",
                              "max": 0.5,
                              "min": 0.1,
                              "value": 0.2
                            },
                            {
                              "id": "T",
                              "max": 3000,
                              "min": 1000,
                              "value": 1500
                            },
                            {
                              "id": "d",
                              "max": 1000,
                              "min": 200,
                              "value": 500
                            }
                          ],
                            "tool":"' . $tool . '"
                        }',
                    true
                )]);
                break;
            case 'T14B':
                $simpleTool->setData([json_decode(
                    '{
                        "parameters": [
                                {
                                  "id": "Qw",
                                  "max": 1000,
                                  "min": 1,
                                  "value": 150
                                },
                                {
                                  "id": "t",
                                  "max": 500,
                                  "min": 100,
                                  "value": 365
                                },
                                {
                                  "id": "S",
                                  "max": 0.5,
                                  "min": 0.1,
                                  "value": 0.2
                                },
                                {
                                  "id": "T",
                                  "max": 3000,
                                  "min": 1000,
                                  "value": 1500
                                },
                                {
                                  "id": "d",
                                  "max": 1000,
                                  "min": 200,
                                  "value": 500
                                },
                                {
                                  "id": "K",
                                  "max": 1000,
                                  "min": 1,
                                  "value": 60
                                },
                                {
                                  "id": "Kdash",
                                  "max": 1,
                                  "min": 0.1,
                                  "value": 0.1
                                },
                                {
                                  "id": "bdash",
                                  "max": 100,
                                  "min": 1,
                                  "value": 1
                                }
                              ],
                            "tool":"' . $tool . '"
                        }',
                    true
                )]);
                break;
            case 'T14C':
                $simpleTool->setData([json_decode(
                    '{
                              "parameters": [
                                {
                                  "id": "Qw",
                                  "max": 1000,
                                  "min": 1,
                                  "value": 150
                                },
                                {
                                  "id": "t",
                                  "max": 500,
                                  "min": 100,
                                  "value": 365
                                },
                                {
                                  "id": "S",
                                  "max": 0.5,
                                  "min": 0.1,
                                  "value": 0.2
                                },
                                {
                                  "id": "T",
                                  "max": 3000,
                                  "min": 1000,
                                  "value": 1500
                                },
                                {
                                  "id": "d",
                                  "max": 1000,
                                  "min": 200,
                                  "value": 500
                                },
                                {
                                  "id": "W",
                                  "max": 10,
                                  "min": 1,
                                  "value": 2.5
                                },
                                {
                                  "id": "Kdash",
                                  "max": 1,
                                  "min": 0.1,
                                  "value": 0.1
                                },
                                {
                                  "id": "bdash",
                                  "max": 10,
                                  "min": 1,
                                  "value": 1
                                }
                              ],                        
                            "tool":"' . $tool . '"
                        }',
                    true
                )]);
                break;
            case 'T14D':
                $simpleTool->setData([json_decode(
                    '{
                        "parameters": [
                            {
                              "id": "Qw",
                              "min": 1,
                              "max": 10000,
                              "value": 150
                            },
                            {
                              "id": "t",
                              "min": 10,
                              "max": 500,
                              "value": 400
                            },
                            {
                              "id": "S",
                              "min": 0.1,
                              "max": 0.5,
                              "value": 0.2
                            },
                            {
                              "id": "T",
                              "min": 1000,
                              "max": 3000,
                              "value": 1500
                            },
                            {
                              "id": "d",
                              "min": 200,
                              "max": 1000,
                              "value": 500
                            },
                            {
                              "id": "W",
                              "min": 1,
                              "max": 10,
                              "value": 2.5
                            },
                            {
                              "id": "Kdash",
                              "min": 0.1,
                              "max": 1,
                              "value": 0.5
                            },
                            {
                              "id": "Bdashdash",
                              "min": 0.1,
                              "max": 20,
                              "value": 7
                            },
                            {
                              "id": "Sigma",
                              "min": 0.1,
                              "max": 0.5,
                              "value": 0.1
                            },
                            {
                              "id": "bdash",
                              "min": 1,
                              "max": 20,
                              "value": 10
                            }
                          ],             
                            "tool":"' . $tool . '"
                        }',
                    true
                )]);
                break;
            default:
                $simpleTool = null;
        }

        return $simpleTool;
    }
}
