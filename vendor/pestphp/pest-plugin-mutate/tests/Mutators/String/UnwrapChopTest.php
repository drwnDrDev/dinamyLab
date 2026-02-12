<?php

declare(strict_types=1);

use Pest\Mutate\Mutators\String\UnwrapChop;

it('unwraps the chop function', function (): void {
    expect(mutateCode(UnwrapChop::class, <<<'CODE'
        <?php

        $b = ($this->asdf)('foo');
        $a = chop('foo', 'f');
        CODE))->toBe(<<<'CODE'
        <?php
        
        $b = ($this->asdf)('foo');
        $a = 'foo';
        CODE);
});
