<?php

namespace Undine\AppBundle\Command;

use Undine\Model\Staff;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;

class StaffCreateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('staff:create')
            ->setDescription('Create a staff member that will have the access to the administration panel.')
            ->addArgument('name', InputArgument::REQUIRED, 'Staff member name.')
            ->addArgument('email', InputArgument::REQUIRED, 'Email address.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $validator = $this->getContainer()->get('validator');
        $name = $input->getArgument('name');
        $email = $input->getArgument('email');

        $dialog = $this->getHelper('dialog');
        $password = $dialog->askHiddenResponse($output, sprintf('<info>Password to use for [<comment>%s</comment>]</info>: ', $email), false);

        $encoder = $this->getContainer()->get('security.encoder_factory')->getEncoder(Staff::class);

        $staff = new Staff($name, $email, $encoder->encodePassword($password, null));

        $violations = $validator->validate($staff);

        if ($violations->count()) {
            $output->writeln('<error>Error</error>');
            foreach ($violations as $violation) {
                /* @var ConstraintViolationInterface $violation */
                $output->writeln(sprintf(' <info>> [<comment>%s</comment>]</info> %s', $violation->getPropertyPath(), $violation->getMessage()));
            }

            return 1;
        }

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $em->persist($staff);
        $em->flush($staff);

        return 0;
    }
}
