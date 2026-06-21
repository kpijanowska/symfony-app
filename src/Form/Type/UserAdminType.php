<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * User admin type.
 *
 * Used by the administrator to create and edit user accounts together with
 * their roles.
 */
class UserAdminType extends AbstractType
{
    /**
     * Builds the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array<string, mixed> $options Form options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $passwordConstraints = $options['require_password']
            ? [new NotBlank(), new Length(min: 6, max: 4096)]
            : [];

        $builder
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'label.email',
                    'required' => true,
                    'attr' => ['maxlength' => 180],
                ]
            )
            ->add(
                'plainPassword',
                PasswordType::class,
                [
                    'label' => 'label.password',
                    'mapped' => false,
                    'required' => $options['require_password'],
                    'constraints' => $passwordConstraints,
                ]
            )
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'label' => 'label.roles',
                    'choices' => [
                        'label.role_admin' => UserRole::ROLE_ADMIN->value,
                    ],
                    'multiple' => true,
                    'expanded' => true,
                ]
            );
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'require_password' => false,
        ]);

        $resolver->setAllowedTypes('require_password', 'bool');
    }

    /**
     * Returns the prefix of the template block name for this type.
     *
     * @return string The prefix of the template block name
     */
    public function getBlockPrefix(): string
    {
        return 'user_admin';
    }
}
